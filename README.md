# php-graphs

[![github action status](https://github.com/hexlet-components/php-graphs/workflows/PHP%20CI/badge.svg)](https://github.com/hexlet-components/php-graphs/actions)

Functions for working with Graphs.

## Examples

```php
<?php

use function Php\Graphs\graphs\makeJoints;
use function Php\Graphs\graphs\buildTreeFromLeaf;
use function Php\Graphs\graphs\sortJoints;

$tree = ['B', [
    ['D'],
    ['A', [
        ['C', [
            ['F'],
            ['E'],
        ]],
    ]],
]];

$joints = makeJoints($tree);
$transformed = buildTreeFromLeaf($joints, 'C');
// ['C', [
//     ['F'],
//     ['E'],
//     ['A', [
//         ['B', [
//             ['D'],
//         ]],
//     ]],
// ]];

sortTree($transformed);
// ['C', [
//     ['A', [
//         ['B', [
//             ['D'],
//         ]],
//     ]],
//     ['E'],
//     ['F'],
// ]];
```

[![Hexlet Ltd. logo](https://raw.githubusercontent.com/Hexlet/assets/master/images/hexlet_logo128.png)](https://hexlet.io?utm_source=github&utm_medium=link&utm_campaign=php-graphs)

This repository is created and maintained by the team and the community of Hexlet, an educational project. [Read more about Hexlet](https://hexlet.io?utm_source=github&utm_medium=link&utm_campaign=php-graphs).
