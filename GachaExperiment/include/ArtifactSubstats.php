<?php


class ArtifactSubstats
{
    const SUBSTATS = [["HP", 6], ["ATK", 6], ["DEF", 6], ["HP%", 4], ["ATK%", 4], ["DEF%", 4],
        ["Energy Recharge", 4], ["Elemental Mastery", 4], ["CRIT DMG", 3], ["CRIT Rate", 3]];

    public array $substats = [];

    /**
     * @param ArtifactMainStat $artifactMainStat
     * @param string $location
     */

    public function __construct(ArtifactMainStat $artifactMainStat, string $location)
    {
        //Strongbox 4-liner probability = 1/3 || Domain 4-liner probability = 1/5
        if ($location === "Strongbox") {
            $random = random_int(1, 3);
        } elseif ($location === "Domain") {
            $random = random_int(1, 5);
        }
        $this->substats = $this->RandomizeSubstats($artifactMainStat, $random);
    }

    private function RandomizeSubstats(ArtifactMainStat $artifactMainStat, int $random): array
    {
        $givenSubstats = [];
        //guard clause to not give substats if it's an offset artifact
        if ($artifactMainStat->mainstat === "Please enter either 'Domain' or 'Strongbox'" || !$artifactMainStat->mainstat) {
            return $givenSubstats;
        }

        $availableSubstats = $this::SUBSTATS;
        //going through the substats to remove the mainstat as you cannot get the same main- and substat
        for ($i = 0; $i < count($availableSubstats); $i++) {
            if ($artifactMainStat->mainstat === $availableSubstats[$i][0]) {
                array_splice($availableSubstats, $i, 1);
            }
        }
        //Giving out random substats, 4 if random number is 1, otherwise 3
        for ($i = 0; $i < (($random === 1) ? 4 : 3); $i++) {
            $weightedProbability = 0;
            for ($j = 0; $j < count($availableSubstats); $j++) {
                $weightedProbability += $availableSubstats[$j][1];
            }
            $randomNumber = random_int(1, $weightedProbability);
            $counter = 0;
            for ($j = 0; $j < count($availableSubstats); $j++) {
                $counter += $availableSubstats[$j][1];
                if ($counter >= $randomNumber) {
                    $givenSubstats[] = [$availableSubstats[$j][0], random_int(7, 10) / 10];
                    array_splice($availableSubstats, $j, 1);
                    break;
                }
            }
        }
        return $givenSubstats;
    }
}