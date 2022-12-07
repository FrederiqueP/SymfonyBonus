<?php
namespace App\Service;
/**
 * Classe qui gère uniquement la grille 2D qui stocke les couleurs de l'avatar 
 *
 * */

class AvatarMatrix {
   
    protected array $listColors;
    protected int $size;
    
    public function __construct(array $listColors, int $size=null)
    {
        $this->listColors = $listColors;
        // l'opérateur récupére la valeur de $size retourne '8'
        // s'il n'existe pas. (équivalent de isset())
        $this->size = $size??8;
        
    }
    public function getSize () {
        return $this->size;
    }

    public function genAvatar() {
        $table_avatar = [];
        $symetrie = $this->size / 2;

        // construire la table
        for ($i = 0 ; $i <= $symetrie ; $i++) {
            for ($j = 0 ; $j < $this->size; $j++) {
                $rand_color = $this->listColors[rand(0, count($this->listColors)-1)];
                $table_avatar [$i][$j] = $rand_color;
                // case miroir ( /:\ attention au premier $size - $i, où $i est à zéro)
                $miror = $this->size -$i -1;
                $table_avatar [$miror][$j] = $rand_color;
            }
        }
        return $table_avatar;
    }
}