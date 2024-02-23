<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use SplMaxHeap;
use SplMinHeap;

class TestController extends Controller
{
    private array $increaseSequence = [
        'prevNumber' => null,
        'longest' => [],
        'current' => [],
    ];
    private array $decreaseSequence = [
        'prevNumber' => null,
        'longest' => [],
        'current' => [],
    ];

    public function __construct(
        private SplMaxHeap $maxHeapForMedian,
        private SplMinHeap $minHeapForMedian,
    )
    {
    }

    public function test(FileRequest $request): JsonResponse
    {
        try {
            $file = $request->file('file');

            $max = $min = $sum = $count = null;

            foreach ($this->readLinesFromFile($file) as $number) {
                $max = $max === null ? $number : max($max, $number);
                $min = $min === null ? $number : min($min, $number);

                $sum += $number;
                $count++;

                $this->addToHeap($number);
                $this->balancingHeaps();
                $this->addToIncreaseSequence($number);
                $this->addToDecreaseSequence($number);
            }

            $average = $sum / $count;
            $median = $this->findMedian();
            $sequences = $this->getSequences();

            return response()->json([
                'max' => $max,
                'min' => $min,
                'average' => $average,
                'median' => $median,
                'longest_increase_sequence' => $sequences['increase'],
                'longest_decrease_sequence' => $sequences['decrease'],
            ]);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    private function addToHeap(int $num): void
    {
        try {
            if ($this->maxHeapForMedian->isEmpty() || $num <= $this->maxHeapForMedian->top()) {
                $this->maxHeapForMedian->insert($num);
            } else {
                $this->minHeapForMedian->insert($num);
            }
        } catch (Exception $e) {
            throw new Exception('Error adding to heap');
        }
    }

    private function balancingHeaps(): void
    {
        try {
            if ($this->maxHeapForMedian->count() - $this->minHeapForMedian->count() > 1) {
                $this->minHeapForMedian->insert($this->maxHeapForMedian->extract());
            } elseif ($this->minHeapForMedian->count() > $this->maxHeapForMedian->count()) {
                $this->maxHeapForMedian->insert($this->minHeapForMedian->extract());
            }
        } catch (Exception $e) {
            throw new Exception('Error balancing heaps');
        }
    }


    private function addToIncreaseSequence(int $number): void
    {
        try {
            if ($this->increaseSequence['prevNumber'] === null || $number >= $this->increaseSequence['prevNumber']) {
                $this->increaseSequence['current'][] = $number;
            } elseif (count($this->increaseSequence['longest']) < count($this->increaseSequence['current'])) {
                $this->increaseSequence['longest'] = $this->increaseSequence['current'];
                $this->increaseSequence['current'] = [$number];
            } else {

                $this->increaseSequence['current'] = [$number];
            }
            $this->increaseSequence['prevNumber'] = $number;
        } catch (Exception $e) {
            throw new Exception('Error adding to increase sequence');
        }
    }

    private function addToDecreaseSequence(int $number): void
    {
        try {
            if ($this->decreaseSequence['prevNumber'] === null || $number <= $this->decreaseSequence['prevNumber']) {
                $this->decreaseSequence['current'][] = $number;
            } elseif (count($this->decreaseSequence['longest']) < count($this->decreaseSequence['current'])) {
                $this->decreaseSequence['longest'] = $this->decreaseSequence['current'];
                $this->decreaseSequence['current'] = [$number];
            } else {
                $this->decreaseSequence['current'] = [$number];
            }
            $this->decreaseSequence['prevNumber'] = $number;
        } catch (Exception $e) {
            throw new Exception('Error adding to decrease sequence');
        }
    }

    private function getSequences(): array
    {
        if (count($this->increaseSequence['longest']) < count($this->increaseSequence['current'])) {
            $this->increaseSequence['longest'] = $this->increaseSequence['current'];
        }

        if (count($this->decreaseSequence['longest']) < count($this->decreaseSequence['current'])) {
            $this->decreaseSequence['longest'] = $this->decreaseSequence['current'];
        }

        return [
            'increase' => $this->increaseSequence['longest'],
            'decrease' => $this->decreaseSequence['longest'],
        ];
    }

    private function findMedian(): float
    {
        try {
            if ($this->maxHeapForMedian->count() == $this->minHeapForMedian->count()) {
                return ($this->maxHeapForMedian->top() + $this->minHeapForMedian->top()) / 2;
            } else {
                return $this->maxHeapForMedian->top();
            }
        } catch (Exception $e) {
            throw new Exception('Error finding median');
        }
    }

    private function readLinesFromFile($filePath): mixed
    {
        $handle = fopen($filePath, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                yield intval(trim($line));
            }
            fclose($handle);
        } else {
            throw new Exception('Failed to open file');
        }
    }

}



