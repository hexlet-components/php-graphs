<?php

namespace Php\Graphs\Tests;

use PHPUnit\Framework\TestCase;

use function Php\Graphs\graphs\makeJoints;
use function Php\Graphs\graphs\buildTreeFromLeaf;
use function Php\Graphs\graphs\sortJoints;
use function Php\Graphs\graphs\map;
use function Php\Graphs\graphs\sortTree;

class GraphsTest extends TestCase
{
    public function testMakeJoints(): void
    {
        $tree = ['A', [
            ['C', [
                ['F', [
                    ['J', [
                        ['O'],
                        ['N'],
                    ]],
                    ['I', [
                        ['M'],
                    ]],
                ]],
                ['G', [
                    ['K'],
                    ['L'],
                ]],
            ]],
            ['B', [
                ['E'],
                ['D', [
                    ['H'],
                ]],
            ]],
        ]];

        $expected = [
            'A' => ['C', 'B', null],
            'C' => ['F', 'G', 'A'],
            'F' => ['J', 'I', 'C'],
            'J' => ['O', 'N', 'F'],
            'O' => ['J'],
            'N' => ['J'],
            'I' => ['M', 'F'],
            'M' => ['I'],
            'G' => ['K', 'L', 'C'],
            'K' => ['G'],
            'L' => ['G'],
            'B' => ['E', 'D', 'A'],
            'E' => ['B'],
            'D' => ['H', 'B'],
            'H' => ['D']
        ];

        $actual = makeJoints($tree);
        $this->assertEquals($expected, $actual);
    }

    public function testBuildTreeFromLeaf(): void
    {
        $joints = [
            'B' => ['D', 'A'],
            'D' => ['B'],
            'A' => ['C', 'B'],
            'C' => ['F', 'E', 'A'],
            'F' => ['C'],
            'E' => ['C'],
        ];

        $expected = ['C', [
            ['F'],
            ['E'],
            ['A', [
                ['B', [
                    ['D']
                ]],
            ]],
        ]];


        $actual = buildTreeFromLeaf($joints, 'C');
        $this->assertEquals($expected, $actual);
    }

    public function testSortJoints(): void
    {
        $joints = [
            'B' => ['D', 'A'],
            'D' => ['B'],
            'A' => ['C', 'B'],
            'C' => ['F', 'E', 'A'],
            'F' => ['C'],
            'E' => ['C'],
        ];

        $expected = [
            'B' => ['A', 'D'],
            'D' => ['B'],
            'A' => ['B', 'C'],
            'C' => ['A', 'E', 'F'],
            'F' => ['C'],
            'E' => ['C'],
        ];

        $actual = sortJoints($joints);

        $this->assertEquals($expected, $actual);
    }

    public function testMap(): void
    {
        $tree = ['A', [
            ['C', [
                ['F', [
                    ['J', [
                        ['O'],
                        ['N'],
                    ]],
                    ['I', [
                        ['M'],
                    ]],
                ]],
                ['G', [
                    ['K'],
                    ['L'],
                ]],
            ]],
            ['B', [
                ['E'],
                ['D', [
                    ['H'],
                ]],
            ]],
        ]];

        $expected = ['a', [
            ['c', [
                ['f', [
                    ['j', [
                        ['o'],
                        ['n'],
                    ]],
                    ['i', [
                        ['m'],
                    ]],
                ]],
                ['g', [
                    ['k'],
                    ['l'],
                ]],
            ]],
            ['b', [
                ['e'],
                ['d', [
                    ['h'],
                ]],
            ]],
        ]];

        $actual = map(function ($node) {
            [$name] = $node;
            return strtolower($name);
        }, $tree);

        $this->assertEquals($expected, $actual);
    }

    public function testSortTree(): void
    {
        $tree = ['B', [
            ['D'],
            ['A', [
                ['C', [
                    ['F'],
                    ['E'],
                ]],
                ['B', [
                    ['D'],
                ]],
            ]],
        ]];

        $expected = ['B', [
            ['A', [
                ['B', [
                    ['D'],
                ]],
                ['C', [
                    ['E'],
                    ['F'],
                ]],
            ]],
            ['D'],
        ]];

        $actual = sortTree($tree);

        $this->assertEquals($expected, $actual);
    }
}
