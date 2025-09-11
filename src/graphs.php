<?php

namespace Php\Graphs\graphs;

use Tightenco\Collect\Support\Collection;

/**
 * Make joints from tree
 * @param array $tree
 * @return array
 * @example
 * $tree = ['B', [
 *    ['D'],
 *    ['A', [
 *        ['C', [
 *            ['F'],
 *            ['E'],
 *       ]],
 *    ]],
 * ]];
 *
 * makeJoints($tree);
 * // [
 * //   'B' => ['D', 'A'],
 * //   'D' => ['B'],
 * //   'A' => ['C', 'B'],
 * //   'C' => ['F', 'E', 'A'],
 * //   'F' => ['C'],
 * //   'E' => ['C'],
 * // ]
 */
function makeJoints(array $tree): array
{
    $iter = function ($tree, $acc, $parent) use (&$iter) {
        $leaf = $tree[0];
        $children = $tree[1] ?? null;

        if (!$children) {
            $acc[$leaf] = [$parent];
            return $acc;
        }

        $neighbors = array_map(fn($child) => $child[0], $children);

        $neighbors[] = $parent;

        $newAcc = array_merge($acc, [$leaf => array_values($neighbors)]);

        return array_reduce($children, function ($iAcc, $n) use (&$iter, $leaf) {
            return $iter($n, $iAcc, $leaf);
        }, $newAcc);
    };

    return $iter($tree, [], '');
}

/**
 * Build tree from leaf
 * @param array $joints
 * @param string $name (leaf name)
 * @return array
 * @example
 * $joints = [
 *     B: ['D', 'A'],
 *     D: ['B'],
 *     A: ['C', 'B'],
 *     C: ['F', 'E', 'A'],
 *     F: ['C'],
 *     E: ['C'],
 * ];
 *
 * buildTreeFromLeaf($joints, 'C');
 * // ['C', [
 * //     ['F'],
 * //     ['E'],
 * //     ['A', [
 * //         ['B', [
 * //             ['D'],
 * //         ]],
 * //     ]],
 * // ]];
 */
function buildTreeFromLeaf(array $joints, string $leaf): array
{
    $iter = function ($current, $acc) use (&$iter, $joints) {

        $checked = [...$acc, $current];
        $neighbors = $joints[$current] ?? [];
        $filtered = array_filter($neighbors, function ($n) use ($checked) {
            return !in_array($n, $checked) && $n !== "";
        });
        $mapped = array_map(function ($n) use (&$iter, $checked) {
            return $iter($n, $checked);
        }, array_values($filtered));

        $result = empty($mapped) ? [$current] : [$current, $mapped];

        return $result;
    };

    $tree = $iter($leaf, []);

    return $tree;
}


/**
 * Sort joints
 * @param array $joints
 * @return array
 * @example
 * $joints = [
 *     B: ['D', 'A'],
 *     D: ['B'],
 *     A: ['C', 'B'],
 *     C: ['F', 'E', 'A'],
 *     F: ['C'],
 *     E: ['C'],
 * ];
 *
 * sortJoints($joints);
 * // [
 * //     B: ['A', 'D'],
 * //     D: ['B'],
 * //     A: ['B', 'C'],
 * //     C: ['A', 'E', 'F'],
 * //     F: ['C'],
 * //     E: ['C'],
 * // ]
 */
function sortJoints(array $joints): array
{
    $sortedJoints = [];
    foreach ($joints as $node => $neighbors) {
        sort($neighbors);
        $sortedJoints[$node] = $neighbors;
    }

    return $sortedJoints;
}

/**
 * Map tree
 * @param callable $func
 * @param array $tree
 * @return array
 * @example
 * $tree = ['B', [
 *     ['D'],
 *     ['A', [
 *         ['C', [
 *             ['F'],
 *             ['E'],
 *         ]],
 *     ]],
 * ]];
 *
 * map(function ($node) {
 *     [$name] = $node;
 *     return strtolower($name);
 * }, $tree);
 * // ['b', [
 * //     ['d'],
 * //     ['a', [
 * //         ['c', [
 * //             ['f'],
 * //             ['e'],
 * //         ]],
 * //     ]],
 * // ]];
 */
function map(callable $func, array $tree): array
{
    $children = $tree[1] ?? null;
    $updatedName = $func($tree);

    if (!$children) {
        return [$updatedName];
    }

    return [
        $updatedName,
        array_map(fn($child) => map($func, $child), $children),
    ];
}

/**
 * Make associations (key-value pairs)
 * @param array $uniqueTree (tree with unique leaf names)
 * @param array $tree
 * @return array
 * @example
 * $tree = ['B', [
 *     ['D'],
 *     ['A', [
 *         ['C', [
 *             ['F'],
 *             ['E'],
 *         ]],
 *     ]],
 * ]];
 *
 * $uniqueTree = map(fn($node) => uniqid($node[0]), $tree);
 * // ['B1', [
 * //     ['D2'],
 * //     ['A3', [
 * //         ['C4', [
 * //             ['F5'],
 * //             ['E6'],
 * //         ]],
 * //     ]],
 * // ]];
 *
 * makeAssociations($uniqueTree, $tree);
 * // [
 * //     B1: 'B',
 * //     D2: 'D',
 * //     A3: 'A',
 * //     C4: 'C',
 * //     F5, 'F',
 * //     E6, 'E',
 * // ]
 */
function makeAssociations(array $uniqueTree, array $tree): array
{
    $uniqueLeafs = collect($uniqueTree)->flatten();
    $leafs = collect($tree)->flatten();

    $result = (object) [];
    $index = -1;
    $length = count($uniqueLeafs);

    while (++$index < $length) {
        $leaf = $leafs[$index] ?? null;
        $result->{$uniqueLeafs[$index]} = $leaf;
    }

    return (array) $result;
}

/**
 * Sorts leafs in a tree (does not change its structure)
 * @param array $tree
 * @return array
 * @example
 * $tree = ['B', [
 *     ['D'],
 *     ['A', [
 *         ['C', [
 *             ['F'],
 *             ['E'],
 *         ]],
 *         ['B', [
 *             ['D'],
 *         ]],
 *     ]],
 * ]];
 *
 * sortTree($tree);
 * // ['B', [
 * //     ['A', [
 * //         ['B', [
 * //             ['D'],
 * //         ]],
 * //         ['C', [
 * //             ['E'],
 * //             ['F'],
 * //         ]],
 * //     ]],
 * //     ['D'],
 * // ]];
 */
function sortTree(array $tree): array
{
    $uniqueTree = map(fn($node) => uniqid($node[0]), $tree);
    $associations = makeAssociations($uniqueTree, $tree);
    [$root] = $uniqueTree;
    $joints = makeJoints($uniqueTree);
    $sortedJoints = sortJoints($joints);

    $sorted = buildTreeFromLeaf($sortedJoints, $root);

    return map(fn($leaf) => $associations[$leaf[0]], $sorted);
}
