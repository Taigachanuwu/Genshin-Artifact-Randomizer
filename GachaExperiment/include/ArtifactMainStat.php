<?php

class ArtifactMainStat
{
    const MAINSTATS_FLOWER = [
        ["HP", 1.0]
    ];
    const MAINSTATS_FEATHER = [
        ["ATK", 1.0]
    ];
    const MAINSTATS_SANDS = [
        ["HP%", 0.2668], ["ATK%", 0.2666], ["DEF%", 0.2666],
        ["Elemental Mastery", 0.1], ["Energy Recharge", 0.1]
    ];
    const MAINSTATS_GOBLET = [
        ["HP%", 0.1925], ["ATK%", 0.1925], ["DEF%", 0.19],
        ["Pyro DMG Bonus", 0.05], ["Electro DMG Bonus", 0.05], ["Cryo DMG Bonus", 0.05],
        ["Hydro DMG Bonus", 0.05], ["Dendro DMG Bonus", 0.05], ["Anemo DMG Bonus", 0.05],
        ["Geo DMG Bonus", 0.05], ["Physical DMG Bonus", 0.05], ["Elemental Mastery", 0.025]
    ];
    const MAINSTATS_CIRCLET = [
        ["HP%", 0.22], ["ATK%", 0.22], ["DEF%", 0.22],
        ["CRIT Rate", 0.1], ["CRIT DMG", 0.1], ["Healing Bonus", 0.1], ["Elemental Mastery", 0.04]
    ];

    public string $mainstat = "";

    /**
     * @param ArtifactType $artifactType
     * @throws Exception
     */

    public function __construct(ArtifactType $artifactType)
    {
        $this->mainstat = $this->Randomizer($artifactType);
    }

    /**
     * @param ArtifactType $artifactType
     * @return string
     * @throws Exception
     */
    private function Randomizer(ArtifactType $artifactType): string
    {
        switch ($artifactType) {
            case ($artifactType->artifactType === "Flower"):
                $possibleMainstats = $this::MAINSTATS_FLOWER;
                break;
            case ($artifactType->artifactType === "Feather"):
                $possibleMainstats = $this::MAINSTATS_FEATHER;
                break;
            case ($artifactType->artifactType === "Sands"):
                $possibleMainstats = $this::MAINSTATS_SANDS;
                break;
            case ($artifactType->artifactType === "Goblet"):
                $possibleMainstats = $this::MAINSTATS_GOBLET;
                break;
            case ($artifactType->artifactType === "Circlet"):
                $possibleMainstats = $this::MAINSTATS_CIRCLET;
                break;
            case ($artifactType->artifactType === "Wrong set."):
                return false;
            default:
                return "Please enter either 'Domain' or 'Strongbox'";
        }
        $random = random_int(1, 1000);
        $counter = 0;
        for ($i = 0; $i < count($possibleMainstats); $i++) {
            $counter += $possibleMainstats[$i][1] * 1000;
            if ($counter >= $random) {
                return $possibleMainstats[$i][0];
            }
        }
        return "Error";
    }
}