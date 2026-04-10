<?php
include 'voiceit/voiceit3.php';
$vi = new VoiceIt\VoiceIt3(getenv('VOICEIT_API_KEY'), getenv('VOICEIT_API_TOKEN'));
$phrase = "never forget tomorrow is a new day";
$td = "test-data";
$errors = 0;

function check($step, $json) {
    global $errors;
    $r = json_decode($json);
    $code = $r->responseCode ?? '?';
    echo ($code === 'SUCC' ? 'PASS' : 'FAIL') . ": $step ($code)\n";
    if ($code !== 'SUCC') $errors++;
    return $r;
}

$r = check("CreateUser", $vi->createUser());
$userId = $r->userId;

for ($i = 1; $i <= 3; $i++) {
    check("VideoEnrollment$i", $vi->createVideoEnrollment($userId, "en-US", $phrase, "$td/videoEnrollmentA$i.mov"));
}

$r = check("VideoVerification", $vi->videoVerification($userId, "en-US", $phrase, "$td/videoVerificationA1.mov"));
echo "  Voice: {$r->voiceConfidence}, Face: {$r->faceConfidence}\n";

check("DeleteEnrollments", $vi->deleteAllEnrollments($userId));
check("DeleteUser", $vi->deleteUser($userId));

if ($errors > 0) { echo "\n$errors FAILURES\n"; exit(1); }
echo "\nAll tests passed!\n";
