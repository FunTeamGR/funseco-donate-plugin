<?php

/*
 * Donations need a United server account. Still placeholders: the coppers buttons, min and
 * public_threshold, and every text (label, help, thanks_*), see the note on messages below.
 */
return [
    'coppers' => [100, 200, 500, 1000, 2000, 5000, 10000],

    // Smallest donation (Nadeo tax) and the amount from which the thanks goes to everybody.
    'min' => 10,
    'public_threshold' => 100,

    // Texts: {1}.. as noted. DONATION, THANKS_* and the help lines are reconstructions of stock
    // XAseco config.xml (the owner's file was not available): replace them with the owner's.
    'messages' => [
        // {1} coppers, {2} server name; shown in the player's payment dialog
        'label' => 'Donate {1} coppers to {2}$z$s?',
        'help' => '{#server}> {#error}Use {#highlite}$i/donate <number>{#error} to donate coppers',
        // {1} minimum
        'minimum' => '{#server}> {#error}Minimum donation is {#highlite}$i {1}{#error} coppers!',
        // {1} server name, {2} coppers, {3} donor nickname
        'thanks_all' => '{#server}> {#highlite}{3}$z$s{#pink_dark} donated {#highlite}{2}{#pink_dark} coppers to {#highlite}{1}{#pink_dark}. Thank you!',
        // {1} coppers
        'thanks_you' => '{#server}> {#pink_dark}Thanks for your {#highlite}{1}{#pink_dark} coppers donation!',
    ],
];
