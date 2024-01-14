<?php

require "include/Character.php";
require "include/Artifact.php";
require "include/ArtifactType.php";
require "include/ArtifactMainStat.php";
require "include/ArtifactSubstats.php";

$testing = new Character();

print_r($testing->CritValueArtifact(53));