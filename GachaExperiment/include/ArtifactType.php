<?php


class ArtifactType
{
    const ARTIFACTS = ["Flower", "Feather", "Sands", "Goblet", "Circlet"];

    /**
     * @param string $artifactType
     * @throws \Exception
     */

    public function __construct(public string $artifactType)
    {

        if ($this->artifactType !== "Domain" && $this->artifactType !== "Strongbox") {
            $this->artifactType = false;
            return;
        } elseif ($this->artifactType === "Domain") {
            // 50/50 chance of onset artifact
            if (random_int(0, 1) == 0) {
                $this->artifactType = "Wrong set.";
                return;
            }
        }
        $this->artifactType = $this::ARTIFACTS[random_int(0, 4)];
    }
}