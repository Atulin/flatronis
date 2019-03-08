<?php
/**
 * Created by PhpStorm.
 * User: Angius
 * Date: 07.03.2019
 * Time: 21:30
 */

namespace App\Helpers;


class Sitemap
{
    private $token;
    private $url;
    private $callback;
    private $frequency;
    private $log;


    /**
     * Sitemap constructor.
     * @param string $callback
     * @param string $frequency
     * @throws \Exception
     */
    public function __construct( string $callback, string $frequency = null )
    {
        $auth = json_decode(self::RequestToken($_ENV['CODEPUNKER']), true)['response'];

        $this->token = $auth;
        $this->url = SETTINGS['domain'];
        $this->callback = $callback;
        $this->frequency = $frequency ?? 'daily';
        $this->log = new Log();
    }


    /**
     * @param string $sitemap_xml
     */
    public static function Create(string $sitemap_xml): void
    {
        $sitemap_url = json_decode($sitemap_xml, true)['response'];
        $sitemap = fopen($sitemap_url, 'rb');

        // Create sitemap.xml
        file_put_contents(ROOT.'/sitemap.xml', $sitemap);

        // Create sitemap.xml.gz
        $gzdata = gzencode($sitemap, 9);
        file_put_contents(ROOT.'/sitemap.xml.gz', $gzdata);

        // Save response in case it's an error
        // file_put_contents('lastresponse.txt', var_export($_POST, true) . "\r\n");

        // Log creation to file
        $log = new Log();
        $log->Write('Sitemap created' . ' ' . var_export($_POST, true));
    }


    /**
     * Sends a request to Codepunker API asking for sitemap generation
     * @return void
     */
    public function GetSitemap(): void
    {
        $url = 'https://www.codepunker.com/tools';
        $data = [
            'execute' => 'executeSitemapGenerator',
            'domain' => $this->url,
            'freq' => $this->frequency,
            'token' => $this->token,
            'callbackuri' => $this->callback
        ];

        // use key 'http' even if you send the request to https://...
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        $this->log->Write("Sitemap requested: $result");
    }


    /**
     * Sends a request to Codepunker API asking for an authorization token
     * @param string $key Takes a Codepunker API key
     * @return string Returns an authorization token or null if authorization failed
     * @throws \Exception
     */
    private static function RequestToken(string $key): string
    {
        $url = 'https://www.codepunker.com/tools';
        $data = [
            'execute' => 'authorizeAPI',
            'key' => $key,
            'rand' => bin2hex(random_bytes(16))
        ];

        // use key 'http' even if you send the request to https://...
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) { return null; }
        return $result;
    }
}
