<?php

class Character
{
    // As the name states, these are the preferred substats for the character Hu Tao.
    // Create a new const for your favorite character with your own custom weights
    // and change the called const in RateArtifact() function.
    const PREFERRED_SUBSTATS_HU_TAO = [
        ["CRIT Rate", 12], ["CRIT DMG", 9]
        , ["Elemental Mastery", 6], ["HP%", 6]
        , ["ATK%", 2], ["HP", 1], ["ATK", 0]
        , ["DEF", 0], ["DEF%", 0], ["Energy Recharge", 0]
    ];
    const SUBSTATS = [["HP", 299], ["ATK", 19], ["DEF", 23], ["HP%", 5.8], ["ATK%", 5.8], ["DEF%", 7.3],
        ["Energy Recharge", 6.5], ["Elemental Mastery", 23], ["CRIT DMG", 3.9], ["CRIT Rate", 7.8]];
    public Artifact|int $equippedFlower = 0;
    public Artifact|int $equippedFeather = 0;
    public Artifact|int $equippedSands = 0;
    public Artifact|int $equippedGoblet = 0;
    public Artifact|int $equippedCirclet = 0;

    public function CreateArtifact(string $location)
    {
        $artifactType = new ArtifactType($location);
        $artifactMainstat = new ArtifactMainStat($artifactType);
        $artifactSubstats = new ArtifactSubstats($artifactMainstat, $location);
        return new Artifact($artifactType, $artifactMainstat, $artifactSubstats);
    }

    public function GetArtifactOptimized($amount, $artifactType, $minimumScore = 50)
    {
        $i = 0;
        $j = 0;
        $days = 0;
        $newArtifact = [];
        while ($j != $amount) {
            do {
                //you can farm roughly 9 domain drops and 4 strongbox drops per day
                if ($i % 13 <= 8) {
                    $newArtifact[] = $this->CreateArtifact("Domain");
                } else {
                    $newArtifact[] = $this->CreateArtifact("Strongbox");
                }
                $newArtifact[$i] = $this->LevelArtifact($newArtifact[$i], 20);
                $score = $this->RateArtifact($newArtifact[$i]);
                $i++;
                if ($i % 13 === 0) {
                    $days++;
                }
            } while ($score < $minimumScore
            || $newArtifact[$i - 1]->artifactType->artifactType !== $artifactType
            || $newArtifact[$i - 1]->artifactMainStat->mainstat !== "Elemental Mastery");
            $j++;
        }
        return [$newArtifact[$i - 1], $i - 1, $days];
    }

    public function GitGudSet($minimumScore, $amountArtifacts)
    {
        $i = 0;
        $days = 0;
        $newArtifact = [];
        $conditionChecker = [5];
        while (array_sum($conditionChecker) > 5 - $amountArtifacts) {
            //you can farm roughly 9 domain drops and 4 strongbox drops per day
            if ($i % 13 <= 8) {
                $newArtifact[] = $this->CreateArtifact("Domain");
            } else {
                $newArtifact[] = $this->CreateArtifact("Strongbox");
            }
            $newArtifact[$i] = $this->LevelArtifact($newArtifact[$i], 20);
            $this->CompareArtifacts($newArtifact[$i]);
            $i++;
            if ($i % 13 === 0) {
                $days++;
            }
            //I hate the fact this works, I won't change a thing though
            $conditionChecker = [($this->RateArtifact($this->equippedFlower) < $minimumScore),
                ($this->RateArtifact($this->equippedFeather) < $minimumScore),
                ($this->RateArtifact($this->equippedSands) < $minimumScore),
                ($this->RateArtifact($this->equippedGoblet) < $minimumScore),
                ($this->RateArtifact($this->equippedCirclet) < $minimumScore)];
        }
        return [$this->equippedFlower, $this->equippedFeather, $this->equippedSands,
            $this->equippedGoblet, $this->equippedCirclet, "rolls" => $i, "days" => $days];
    }

    public function CritValueArtifact($minimumCritValue)
    {
        while (true) {
            $artifact = $this->CreateArtifact("Domain");
            if ($this->IsDoubleCritFourLiner($artifact)) {
                $artifact = $this->LevelArtifact($artifact, 20);
                $i = 0;
                $critSubstatIndexes = [];
                foreach ($artifact->artifactSubstats->substats as $substat) {
                    if ($substat[0] === "CRIT Rate" || $substat[0] === "CRIT DMG") {
                        $critSubstatIndexes[] = $i;
                    }
                    $i++;
                }
                print_r($critSubstatIndexes);
                $critValue = $artifact->artifactSubstats->substats[$critSubstatIndexes[0]][1] + $artifact->artifactSubstats->substats[$critSubstatIndexes[1]][1];
                if ($critValue * 7.77 >= $minimumCritValue) {
                    return $artifact;
                }
            }
        }
    }

    private function IsDoubleCritFourLiner(Artifact $artifact): bool
    {
        $critSubstats = 0;
        foreach ($artifact->artifactSubstats->substats as $substat) {
            if ($substat[0] === "CRIT Rate" || $substat[0] === "CRIT DMG") {
                $critSubstats++;
            }
        }
        if ($critSubstats < 2 || count($artifact->artifactSubstats->substats) < 4) {
            return false;
        }
        return true;
    }

    public function CompareArtifacts(Artifact $artifact)
    {
        //preferred mainstats are currently hard coded into functions, lookup table would be better
        switch ($artifact) {
            case $artifact->artifactType->artifactType === "Flower":
                $comparisonArtifact = $this->equippedFlower;
                break;
            case $artifact->artifactType->artifactType === "Feather":
                $comparisonArtifact = $this->equippedFeather;
                break;
            case $artifact->artifactType->artifactType === "Sands":
                if ($artifact->artifactMainStat->mainstat !== "Elemental Mastery") {
                    return;
                }
                $comparisonArtifact = $this->equippedSands;
                break;
            case $artifact->artifactType->artifactType === "Goblet":
                if ($artifact->artifactMainStat->mainstat !== "Pyro DMG Bonus") {
                    return;
                }
                $comparisonArtifact = $this->equippedGoblet;
                break;
            case $artifact->artifactType->artifactType === "Circlet":
                if ($artifact->artifactMainStat->mainstat !== "CRIT Rate") {
                    return;
                }
                $comparisonArtifact = $this->equippedCirclet;
                break;
            default:
                return;
        }

        $ratingNewArtifact = $this->RateArtifact($artifact);
        $ratingOldArtifact = $this->RateArtifact($comparisonArtifact);

        //re-entering new artifact into respective placeholder
        if ($ratingNewArtifact > $ratingOldArtifact) {
            switch ($artifact) {
                case $artifact->artifactType->artifactType === "Flower":
                    $this->equippedFlower = $artifact;
                    break;
                case $artifact->artifactType->artifactType === "Feather":
                    $this->equippedFeather = $artifact;
                    break;
                case $artifact->artifactType->artifactType === "Sands":
                    $this->equippedSands = $artifact;
                    break;
                case $artifact->artifactType->artifactType === "Goblet":
                    $this->equippedGoblet = $artifact;
                    break;
                case $artifact->artifactType->artifactType === "Circlet":
                    $this->equippedCirclet = $artifact;
                    break;
            }
        }
    }

    //supposed to level both Mainstat and Substats, only levels substats currently
    public function LevelArtifact(Artifact $artifact, int $level)
    {
        if ($artifact->artifactSubstats->substats === []) {
            return $artifact;
        }

        for ($i = 1; $i <= $level; $i++) {
            //leaving some space here to make leveling the mainstat possible too

            if ($i % 4 === 0) {
                $this->LevelSubstat($artifact);
            }
        }
        return $artifact;
    }

    private function LevelSubstat(Artifact $artifact)
    {
        if (count($artifact->artifactSubstats->substats) === 3) {
            $artifact->artifactSubstats->substats = $this->AddSubstat($artifact->artifactMainStat, $artifact->artifactSubstats);
        } else {
            $random = random_int(0, 3);
            $artifact->artifactSubstats->substats[$random][1] += random_int(7, 10) / 10;
        }
    }

    public function RateArtifact(Artifact|int $artifact)
    {
        if ($artifact === 0) {
            return 0;
        }
        $ratingTable = $this::PREFERRED_SUBSTATS_HU_TAO;
        $rating = 0;
        foreach ($artifact->artifactSubstats->substats as $substat) {
            for ($i = 0; $i < count($ratingTable); $i++) {
                if ($substat[0] === $ratingTable[$i][0]) {
                    $rating += $ratingTable[$i][1] * $substat[1];
                    break;
                }
            }
        }
        return $rating;
    }

    public function AddSubstat(ArtifactMainStat $artifactMainStat, ArtifactSubstats $artifactSubstats)
    {
        $availableSubstats = ArtifactSubstats::SUBSTATS;
        foreach ($artifactSubstats->substats as $substat) {
            $i = 0;
            while ($i < count($availableSubstats)) {
                if ($substat[0] === $availableSubstats[$i][0]) {
                    array_splice($availableSubstats, $i, 1);
                    break;
                }
                $i++;
            }
        }
        $i = 0;
        while ($i < count($availableSubstats)) {
            if ($artifactMainStat->mainstat === $availableSubstats[$i][0]) {
                array_splice($availableSubstats, $i, 1);
            }
            $i++;
        }

        $givenSubstats = $artifactSubstats->substats;
        $weightedProbability = 0;
        for ($j = 0; $j < count($availableSubstats); $j++) {
            $weightedProbability += $availableSubstats[$j][1];
        }
        $randomNumber = random_int(1, $weightedProbability);
        $weighedProbabilityCounter = 0;
        for ($j = 0; $j < count($availableSubstats); $j++) {
            $weighedProbabilityCounter += $availableSubstats[$j][1];
            if ($weighedProbabilityCounter >= $randomNumber) {
                $givenSubstats[] = [$availableSubstats[$j][0], random_int(7, 10) / 10];
                array_splice($availableSubstats, $j, 1);
                break;
            }
        }
        return $givenSubstats;
    }
}