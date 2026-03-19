<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkerController extends Controller
{
    public function dashboard()
    {
        // 顯示所有尚未完成的產品
        $products = Product::with(['processes.processType', 'processes.worker'])
            ->where('status', '!=', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('worker.dashboard', compact('products'));
    }

    public function startProcess($id)
    {
        $process = ProductProcess::findOrFail($id);
        
        // 檢查是否已經有人在做
        if ($process->status !== 'pending') {
            return redirect()->back()->with('error', '該工序已在進行中或已完成。');
        }

        $process->update([
            'status' => 'processing',
            'worker_id' => Auth::id(),
            'started_at' => now(),
        ]);

        // 更新產品狀態
        $process->product->update(['status' => 'processing']);

        return redirect()->back()->with('success', '已領取工序：' . $process->processType->name);
    }

    public function completeProcess($id)
    {
        $process = ProductProcess::findOrFail($id);
        
        if ($process->status !== 'processing') {
            return redirect()->back()->with('error', '只有進行中的工序可以回報完成。');
        }

        $process->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // 檢查產品是否所有工序都完成了
        $product = $process->product;
        $remaining = $product->processes()->where('status', '!=', 'completed')->count();
        if ($remaining === 0) {
            $product->update(['status' => 'completed']);
        }

        return redirect()->back()->with('success', '已完成工序：' . $process->processType->name);
    }

    public function rollbackProcess($id)
    {
        $process = ProductProcess::findOrFail($id);
        
        if ($process->status === 'pending') {
            return redirect()->back()->with('error', '未開始的工序無法回退。');
        }

        if ($process->status === 'processing') {
            // 退回 pending
            $process->update([
                'status' => 'pending',
                'worker_id' => null,
                'started_at' => null,
            ]);
            
            // 檢查產品是否還有其他在進行中的工序，若無則將產品狀態改回 pending
            $product = $process->product;
            $anyProcessing = $product->processes()->where('status', 'processing')->exists();
            if (!$anyProcessing) {
                $product->update(['status' => 'pending']);
            }

            return redirect()->back()->with('success', '工序已退回至待處理狀態。');
        }

        if ($process->status === 'completed') {
            // 退回 processing
            $process->update([
                'status' => 'processing',
                'completed_at' => null,
            ]);

            // 確保產品狀態是 processing
            $process->product->update(['status' => 'processing']);

            return redirect()->back()->with('success', '工序已退回至進行中狀態。');
        }

        return redirect()->back();
    }
}
