<?php

namespace Deployer;

$makeStatusCakeRequest = static function (
    array $data = []
) {
    $missingVarError = 'Please set »%s« variable.';
    $apiUrl          = get('statuscake_api_url', '');
    $apiKey          = get('statuscake_api_key', '');
    $apiUsername     = get('statuscake_api_user', '');

    if (empty($apiUrl)) {
        writeln(sprintf($missingVarError, 'statuscake_api_url'));

        return;
    }
    if (empty($apiKey)) {
        writeln(sprintf($missingVarError, 'statuscake_api_key'));

        return;
    }
    if (empty($apiUsername)) {
        writeln(sprintf($missingVarError, 'statuscake_api_user'));

        return;
    }

    $curl = curl_init($apiUrl.'/Tests/Update'.'?'.http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt(
        $curl,
        CURLOPT_HTTPHEADER,
        [
            'API: '.$apiKey,
            'Username: '.$apiUsername,
        ]
    );

    return curl_exec($curl);
};

set('statuscake_api_url', 'https://app.statuscake.com/API');
set('statuscake_api_key', '');
set('statuscake_api_user', '');
set('statuscake_tests', []);

desc('StatusCake: Pause tasks');
task(
    'deploy:ellinaut:statuscake:task:pause',
    function () use ($makeStatusCakeRequest) {
        $tests = get('statuscake_tests', []);

        if (count($tests) === 0) {
            writeln('Skiping StatusCake updates: No TestIDs given.');

            return;
        }

        foreach ($tests as $testId) {
            $data = [
                'TestID' => $testId,
                'Paused' => 1,
            ];

            $response = json_decode($makeStatusCakeRequest($data));

            if ($response) {
                writeln('Test '.$testId.': '.$response->Message);
            }
        }


    }
);

desc('StatusCake: Pause resume');
task(
    'deploy:ellinaut:statuscake:task:resume',
    function () use ($makeStatusCakeRequest) {
        $tests = get('statuscake_tests', []);

        if (count($tests) === 0) {
            writeln('Skiping StatusCake updates: No TestIDs given.');

            return;
        }

        foreach ($tests as $testId) {
            $data = [
                'TestID' => $testId,
                'Paused' => 0,
            ];

            $response = json_decode($makeStatusCakeRequest($data));

            if ($response) {
                writeln('Test '.$testId.': '.$response->Message);
            }
        }


    }
);


