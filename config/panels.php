<?php

/*
 * Donate panel templates (XAseco panels/Donate*.xml), merged into config('panels.donate') by mergeConfigFrom, a shallow
 * array_merge: a template name the app defines under 'donate' in config/panels.php replaces that whole template (the rest stay).
 * Keys are the template names without the "Donate" prefix.
 * Donation buttons run /donate <value>; values come from config('donate.coppers'). Display only.
 * 'buttons': x of each button, then per-button widths; 'edge' templates stack the buttons downwards.
 */

return [
    'BelowCPListRM' => [
        'frame' => '10.4 -45.6 0', 'bg' => [38.6, 2.5], 'coin' => [1.5, -0.3, 2], 'label' => [4.4, -0.6, '$ccc$s$oDonate:$fff'],
        'x' => [10.5, 13.7, 17, 20.6, 24.2, 28, 32], 'w' => [2.4, 2.4, 3, 3, 3, 4, 4], 'h' => 1.5, 'y' => -0.6,
        'texts' => ['$s$abc%dC', '$s$aaa%dC', '$s$a98%dC', '$s$a86%dC', '$s$a74%dC', '$s$a62%dC', '$s$a50%dC'],
        'button_style' => 'TextRaceChrono', 'scale' => 1.25,
    ],
    'BelowCPList' => [
        'frame' => '10.4 -45.6 0', 'bg' => [38.6, 2.5], 'coin' => [1.5, -0.3, 2], 'label' => [4.5, -0.6, '$f6fDonate:'],
        'x' => [10.5, 13.7, 17, 20.6, 24.2, 28, 32], 'w' => [2.4, 2.4, 3, 3, 3, 4, 4], 'h' => 1.5, 'y' => -0.6,
        'texts' => ['%d C'], 'button_style' => null, 'scale' => 1,
    ],
    'TopLeft' => [
        'frame' => '-64.2 48.2 0', 'bg' => [39.5, 2.5], 'coin' => [1, -0.2, 2], 'label' => [4, -0.4, '$f6fDonate:'],
        'x' => [10, 13.5, 17, 21, 25, 29, 34], 'w' => [2.4, 2.4, 3, 3, 3, 4, 4], 'h' => 1.5, 'y' => -0.4,
        'texts' => ['%d C'], 'button_style' => null, 'scale' => 1,
    ],
    'LeftEdge' => [
        'frame' => '-64.8 5.5 0', 'bg' => [7.5, 29.6], 'coin' => [2, -0.5, 4], 'label' => [1, -5, '$f6fDonate:'], 'label_textsize' => 2,
        'right' => 6.5, 'ys' => [-8, -11, -14, -17, -20, -23, -26], 'w' => [3.8, 3.8, 4.8, 4.8, 4.8, 5.8, 5.8], 'h' => 2,
        'texts' => ['%d C'], 'button_style' => null, 'textsize' => 2, 'scale' => 1,
    ],
    'RightEdge' => [
        'frame' => '57.2 5.5 0', 'bg' => [7.5, 29.6], 'coin' => [1.6, -0.5, 4], 'label' => [0.5, -5, '$f6fDonate:'], 'label_textsize' => 2,
        'right' => 6.3, 'ys' => [-8, -11, -14, -17, -20, -23, -26], 'w' => [3.8, 3.8, 4.8, 4.8, 4.8, 5.8, 5.8], 'h' => 2,
        'texts' => ['%d C'], 'button_style' => null, 'textsize' => 2, 'scale' => 1,
    ],
    'LeftEdge2' => [
        'frame' => '-64.8 5.5 0', 'bg' => [7.5, 29.6], 'coin' => [2, -0.5, 4], 'label' => [1, -5, '$f6fDonate:'], 'label_style' => 'TextCardSmallScores2Rank',
        'right' => 6.5, 'ys' => [-8, -11, -14, -17, -20, -23, -26], 'w' => [3.8, 3.8, 4.8, 4.8, 4.8, 5.8, 5.8], 'h' => 2,
        'texts' => ['%d C'], 'button_style' => 'TextCardSmallScores2', 'scale' => 1,
    ],
    'RightEdge2' => [
        'frame' => '57.2 5.5 0', 'bg' => [7.5, 29.6], 'coin' => [1.6, -0.5, 4], 'label' => [0.5, -5, '$f6fDonate:'], 'label_style' => 'TextCardSmallScores2Rank',
        'right' => 6.3, 'ys' => [-8, -11, -14, -17, -20, -23, -26], 'w' => [3.8, 3.8, 4.8, 4.8, 4.8, 5.8, 5.8], 'h' => 2,
        'texts' => ['%d C'], 'button_style' => 'TextCardSmallScores2', 'scale' => 1,
    ],
    'LeftSmall' => [
        'frame' => '-64.8 -4.5 0', 'bg' => [7.7, 19.5], 'coin' => null, 'label' => [1, -0.8, '$f6fDonate:'], 'label_style' => 'TextCardSmallScores2Rank',
        'right' => 6.5, 'ys' => [-3.5, -5.7, -7.9, -10.1, -12.3, -14.5, -16.7], 'w' => [3.8, 3.8, 4.8, 4.8, 4.8, 5.8, 5.8], 'h' => 2,
        'texts' => ['%d C'], 'button_style' => 'TextCardSmallScores2', 'scale' => 1,
    ],
    'RightSmall' => [
        'frame' => '57.2 -4.5 0', 'bg' => [7.7, 19.5], 'coin' => null, 'label' => [0.5, -0.8, '$f6fDonate:'], 'label_style' => 'TextCardSmallScores2Rank',
        'right' => 6.3, 'ys' => [-3.5, -5.7, -7.9, -10.1, -12.3, -14.5, -16.7], 'w' => [3.8, 3.8, 4.8, 4.8, 4.8, 5.8, 5.8], 'h' => 2,
        'texts' => ['%d C'], 'button_style' => 'TextCardSmallScores2', 'scale' => 1,
    ],
];
