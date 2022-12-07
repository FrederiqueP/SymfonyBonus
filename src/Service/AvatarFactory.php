<?php
namespace App\Service;
/**
 * AvatarFactory la classe qui va s'occuper de générer des couleurs aléatoires
 * et qui va instancier les 2 autres,
 */

class AvatarFactory {

    private int $size;
    private int $nbColor;

    public function __construct(int $nbColor=null, int $size=null)
    {
        $this->nbColor = $nbColor??2;
        $this->size = $size??8;
    }

    public static function randColor() {
        // couleur pastel
        $red   = mt_rand(186, 255);
        $green = mt_rand(186, 255);
        $blue  = mt_rand(186, 255);
        return 'rgb('. $red . ', ' . $green . ', ' . $red . ')';
    }
 
    public static function new(int $size, int $nbColor)
    {
        // la liste de couleurs 
        $listColors = [];
        for($i = 0 ; $i < $nbColor ; $i++) {
            // attention au methode static agit sur variable de classe
            // self::$variable, pour accéder à une variable de la classe parente
            // (attention, les variables doivent être statiques)
            $listColors[$i] = self::randColor();
        }
        // Instanciation Avatar , génération
        $avatarmatrix = new AvatarMatrix($listColors, $size);

        // Instanciation Affichage Avatar 
        $show = new SvgAvatarRenderer();
        $svg = $show->showAvatar($avatarmatrix);
        return $svg;

    }
    

}