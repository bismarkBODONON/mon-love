<?php

namespace Database\Seeders;

use App\Models\Quote;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        $quotes = [
            ['content' => "Aimer, ce n'est pas se regarder l'un l'autre, c'est regarder ensemble dans la même direction.", 'author' => 'Antoine de Saint-Exupéry'],
            ['content' => "Il n'y a qu'un bonheur dans la vie, c'est d'aimer et d'être aimé.", 'author' => 'George Sand'],
            ['content' => "Toi et moi, c'est pour la vie."],
            ['content' => "Chaque jour à tes côtés est un cadeau que je chéris."],
            ['content' => "Tu es la plus belle chose qui me soit arrivée."],
        ];

        foreach ($quotes as $quote) {
            Quote::create($quote);
        }
    }
}