<?php
/* Not implemented, code below incomplete and not working.
include __DIR__ . '/../../../phpOMS/Autoloader.php';
include __DIR__ . '/../db.php';
include __DIR__ . '/../config.php';

use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\Rest;
use phpOMS\Uri\HttpUri;

function authenticate($email, $password)
{
    // Service Authentication
    $request = new HttpRequest(new HttpUri('https://public-ubiservices.ubi.com/v3/profiles/sessions'));
    $request->header->set('Content-Type', 'application/json');
    $request->header->set('Ubi-AppId', '86263886-327a-4328-ac69-527f0d20a237');
    $request->header->set('Authorization', 'Basic ' . \base64_encode($email . ':' . $password));
    $request->header->set('User-Agent', 'COTD ranking / ' . $email);
    $request->setMethod('POST');
    $response = Rest::request($request);

    $request = new HttpRequest(new HttpUri('https://prod.trackmania.core.nadeo.online/v2/authentication/token/ubiservices'));
    $request->header->set('Content-Type', 'application/json');
    $request->header->set('Authorization', 'ubi_v1 t=' . \trim($response->data['ticket'] ?? ''));
    $request->header->set('User-Agent', 'COTD ranking / ' . $email);
    $request->setMethod('POST');
    $request->data['audience'] = 'NadeoClubServices';

    return Rest::request($request);
}

$authResponse = authenticate($email, $password);
if ($authResponse->header->status !== 200) {
    exit;
}

$error = 0;
$matchId = 0;
while (true) {
    ++$matchId;

    if ($error > 10) {
        break;
    }

    $matchInfoRequest = new HttpRequest(new HttpUri('https://meet.trackmania.nadeo.club/api/competitions/' . $matchId));
    $matchInfoRequest->header->set('Content-Type', 'application/json');
    $matchInfoRequest->header->set('Authorization', 'nadeo_v1 t=' . \trim($authResponse->data['accessToken'] ?? ''));
    $matchInfoRequest->header->set('User-Agent', 'COTD ranking / ' . $email);
    $matchInfoRequest->data['audience'] = 'NadeoClubServices';
    $matchInfoRequest->setMethod('GET');
    $matchInfoResponse = Rest::request($matchInfoRequest);

    if ($matchInfoResponse->header->status !== 200) {
        echo "Invalid match response\n";

        $authResponse = authenticate($email, $password);

        $matchInfoRequest = new HttpRequest(new HttpUri('https://meet.trackmania.nadeo.club/api/competitions/' . $matchId));
        $matchInfoRequest->header->set('Content-Type', 'application/json');
        $matchInfoRequest->header->set('Authorization', 'nadeo_v1 t=' . \trim($authResponse->data['accessToken'] ?? ''));
        $matchInfoRequest->header->set('User-Agent', 'COTD ranking / ' . $email);
        $matchInfoRequest->data['audience'] = 'NadeoClubServices';
        $matchInfoRequest->setMethod('GET');
        $matchInfoResponse = Rest::request($matchInfoRequest);

        if ($matchInfoResponse->header->status !== 200) {
            \sleep(1);
            ++$error;

            continue;
        }
    }

    if (empty($matchInfoResponse->data)) {
        echo "Invalid match response\n";
        ++$error;

        continue;
    }

    if ((!\str_starts_with($matchInfoResponse->data['name'], 'COTD ') && !\str_starts_with($matchInfoResponse->data['name'], 'Cup of the Day '))
        || $matchInfoResponse->data['nbPlayers'] < 150
    ) {
        continue;
    }

    $error = 0;
    $liveId = $matchInfoResponse->data['liveId'];

    $playerOffset = -250;
    while (true) {
        $playerOffset += 250;

        $cotdInfoRequest = new HttpRequest(new HttpUri('https://meet.trackmania.nadeo.club/api/competitions/' . $liveId . '/leaderboard?length=250&offset=' . $playerOffset));
        $cotdInfoRequest->header->set('Content-Type', 'application/json');
        $cotdInfoRequest->header->set('Authorization', 'nadeo_v1 t=' . \trim($authResponse->data['accessToken'] ?? ''));
        $cotdInfoRequest->header->set('User-Agent', 'COTD ranking / ' . $email);
        $cotdInfoRequest->data['audience'] = 'NadeoClubServices';
        $cotdInfoRequest->setMethod('GET');
        $cotdInfoResponse = Rest::request($cotdInfoRequest);

        if ($cotdInfoResponse->header->status !== 200) {
            echo "Invalid cotd response\n";

            $authResponse = authenticate($email, $password);

            $cotdInfoRequest = new HttpRequest(new HttpUri('https://meet.trackmania.nadeo.club/api/competitions/' . $liveId . '/leaderboard?length=250&offset=' . $playerOffset));
            $cotdInfoRequest->header->set('Content-Type', 'application/json');
            $cotdInfoRequest->header->set('Authorization', 'nadeo_v1 t=' . \trim($authResponse->data['accessToken'] ?? ''));
            $cotdInfoRequest->header->set('User-Agent', 'COTD ranking / ' . $email);
            $cotdInfoRequest->data['audience'] = 'NadeoClubServices';
            $cotdInfoRequest->setMethod('GET');
            $cotdInfoResponse = Rest::request($cotdInfoRequest);

            if ($cotdInfoResponse->header->status !== 200) {
                break;
            }
        }

        if (empty($cotdInfoResponse->data)) {
            echo "Invalid cotd response\n";

            break;
        }

        foreach ($cotdInfoResponse->data as $player) {
            if ($player['rank'] === 1) {
                $a = 1;
            }

        }
    }
}
*/
