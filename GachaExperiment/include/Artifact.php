<?php


class Artifact
{
    /**
     * @param ArtifactType $artifactType
     * @param ArtifactMainStat $artifactMainStat
     * @param ArtifactSubstats $artifactSubstats
     */
    public function __construct(public ArtifactType     $artifactType, public ArtifactMainStat $artifactMainStat,
                                public ArtifactSubstats $artifactSubstats)
    {
    }
}