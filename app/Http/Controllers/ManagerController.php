<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\ProcessType;
use App\Models\ProductProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
    // === 使用者管理 ===
    public function userIndex()
    {
        $users = User::all();
        return view('manager.users', compact('users'));
    }

    public function userStore(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'name' => 'required',
            'password' => 'required|min:4',
            'role' => 'required|in:manager,worker',
        ]);

        User::create([
            'username' => $request->username,
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->back()->with('success', '使用者已新增');
    }

    public function userDestroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', '使用者已刪除');
    }

    // === 產品管理 ===
    public function productIndex()
    {
        $products = Product::with('processes.processType')->get();
        $processTypes = ProcessType::all();
        return view('manager.products', compact('products', 'processTypes'));
    }

    public function productStore(Request $request)
    {
        $request->validate([
            'product_code' => 'required|unique:products',
            'sales_rep' => 'required',
            'process_ids' => 'required|array',
        ]);

        DB::transaction(function () use ($request) {
            $product = Product::create([
                'product_code' => $request->product_code,
                'sales_rep' => $request->sales_rep,
                'status' => 'pending',
            ]);

            foreach ($request->process_ids as $index => $processTypeId) {
                ProductProcess::create([
                    'product_id' => $product->id,
                    'process_type_id' => $processTypeId,
                    'sequence' => $index + 1,
                    'status' => 'pending',
                ]);
            }
        });

        return redirect()->back()->with('success', '產品已上架');
    }

    public function productEdit(Product $product)
    {
        $product->load('processes.processType');
        $processTypes = ProcessType::all();
        return view('manager.product_edit', compact('product', 'processTypes'));
    }

    public function productUpdate(Request $request, Product $product)
    {
        $request->validate([
            'product_code' => 'required|unique:products,product_code,' . $product->id,
            'sales_rep' => 'required',
            'process_ids' => 'required|array',
        ]);

        DB::transaction(function () use ($request, $product) {
            $product->update([
                'product_code' => $request->product_code,
                'sales_rep' => $request->sales_rep,
            ]);

            // 重新更新製程流 (如果是進行中，需小心處理。目前的邏輯是直接刪掉舊的，重新建立)
            // 建議僅能修改尚未開始的工單，或是在編輯時保留已完成的。
            // 這裡採取簡單作法：保留已完成或進行中的狀態 (如果 ID 沒變的話)，但這比較複雜。
            // 為了簡化原型：我們刪除所有 pending 且重新排，但如果工單已在進行中，則不建議大改。
            
            // 先刪除所有舊的
            $product->processes()->delete();

            foreach ($request->process_ids as $index => $processTypeId) {
                ProductProcess::create([
                    'product_id' => $product->id,
                    'process_type_id' => $processTypeId,
                    'sequence' => $index + 1,
                    'status' => 'pending',
                ]);
            }
        });

        return redirect()->route('manager.products')->with('success', '產品已更新');
    }

    public function productDestroy(Product $product)
    {
        $product->delete(); // 會自動刪除關聯的 product_processes 嗎？視 Migration 而定
        return redirect()->back()->with('success', '產品已刪除');
    }

    // === 製程選項管理 ===
    public function processTypeIndex()
    {
        $processTypes = ProcessType::all();
        return view('manager.process_types', compact('processTypes'));
    }

    public function processTypeStore(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:process_types',
        ]);

        ProcessType::create([
            'name' => $request->name,
            'is_default' => false,
        ]);

        return redirect()->back()->with('success', '製程選項已新增');
    }

    public function processTypeDestroy(ProcessType $processType)
    {
        $processType->delete();
        return redirect()->back()->with('success', '製程選項已刪除');
    }

    // === 進度查看 ===
    public function progressIndex()
    {
        //從資料庫調資料 app/Models/Product
        //processes是Product的方法
        $products = Product::with(['processes.processType', 'processes.worker'])->get();
        //把資料$products丟到前端 views/manager/progress
        return view('manager.progress', compact('products'));
    }
}
