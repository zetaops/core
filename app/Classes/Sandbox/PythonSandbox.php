<?php

namespace App\Classes\Sandbox;

use App\Permission;
use App\Token;
use App\UserSettings;
use Illuminate\Support\Str;

class PythonSandbox implements Sandbox
{
    private $path = "/liman/sandbox/python/index.py";
    private $fileExtension = ".html.ninja";

    public function getPath()
    {
        return $this->path;
    }

    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    public function command($function, $extensionDb = null)
    {
        $combinerFile = $this->path;

        $settings = UserSettings::where([
            "user_id" => user()->id,
            "server_id" => server()->id,
        ]);

        $extensionDb = [];
        foreach ($settings->get() as $setting) {
            $key = env('APP_KEY') . user()->id . extension()->id . server()->id;
            $decrypted = openssl_decrypt($setting->value, 'aes-256-cfb8', $key);
            $stringToDecode = substr($decrypted, 16);
            $extensionDb[$setting->name] = base64_decode($stringToDecode);
        }

        $extensionDb = json_encode($extensionDb);

        $request = request()->except([
            "permissions",
            "extension",
            "server",
            "script",
            "server_id",
        ]);
        $request = json_encode($request);

        $apiRoute = route('extension_server', [
            "extension_id" => extension()->id,
            "city" => server()->city,
            "server_id" => server()->id,
        ]);

        $navigationRoute = route('extension_server', [
            "server_id" => server()->id,
            "extension_id" => extension()->id,
            "city" => server()->city,
        ]);

        $token = Token::create(user()->id);

        if (!user()->isAdmin()) {
            $extensionJson = json_decode(
                file_get_contents(
                    "/liman/extensions/" .
                        strtolower(extension()->name) .
                        DIRECTORY_SEPARATOR .
                        "db.json"
                ),
                true
            );
            $permissions = [];
            if (array_key_exists("functions", $extensionJson)) {
                foreach ($extensionJson["functions"] as $item) {
                    if (
                        Permission::can(
                            user()->id,
                            "function",
                            "name",
                            strtolower(extension()->name),
                            $item["name"]
                        ) ||
                        $item["isActive"] != "true"
                    ) {
                        array_push($permissions, $item["name"]);
                    }
                }
            }
            $permissions = json_encode($permissions);
        } else {
            $permissions = "admin";
        }

        $userData = [
            "id" => user()->id,
            "name" => user()->name,
            "email" => user()->email,
        ];

        $functionsPath =
            "/liman/extensions/" .
            strtolower(extension()->name) .
            "/views/functions.py";

        $publicPath = route('extension_public_folder', [
            "extension_id" => extension()->id,
            "path" => "",
        ]);

        $isAjax = request()->wantsJson() ? true : false;
        $array = [
            $functionsPath,
            $function,
            server()->toArray(),
            extension()->toArray(),
            $extensionDb,
            $request,
            $apiRoute,
            $navigationRoute,
            $token,
            $permissions,
            session('locale'),
            json_encode($userData),
            $publicPath,
            $isAjax,
        ];

        $keyPath = '/liman/keys' . DIRECTORY_SEPARATOR . extension()->id;
        $combinerFile =
            "/liman/extensions/" .
            strtolower(extension()->name) .
            "/views/functions.py";
        $encrypted = base64_encode(json_encode($array));
        return "sudo -u " .
            cleanDash(extension()->id) .
            " bash -c 'export PYTHONPATH=\$PYTHONPATH:/liman/sandbox/python; timeout 30 /usr/bin/python3 $combinerFile $keyPath $encrypted 2>&1'";
    }

    public function getInitialFiles()
    {
        return ["index.blade.php", "functions.py"];
    }
}