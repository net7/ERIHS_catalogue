<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class RorService
{
    private $rorBaseUrl;
    private $organizationUrl = 'organizations';

    public function __construct()
    {
        $this->rorBaseUrl = env('ROR_BASE_URL', 'https://api.ror.org/v2/');
    }
    public function retrieveOrganizationsByName($name)
    {
        $parsedResponse = [];
        $acronyms = [];
        $names =[];
        $response = Http::get($this->rorBaseUrl . $this->organizationUrl . "?query.advanced=names.value:" . $name);
        if ($response->status() == 200) {
            foreach ($response['items'] as $item) {
                $name = null;
                $acronym = '';
                foreach($item['names'] as $n){
                    if (in_array('ror_display', $n['types'])){
                        $name = $n['value'];
                    }
                    if (in_array('acronym', $n['types'])){
                        $acronym = $n['value'];
                    }
                }
                if ($name == null){
                    $name = $item['names'][0]['value'];
                }

                $names[$item['id']] = $name;
                $acronyms[$item['id']] = $acronym;
            }
        } else {
            throw new Exception("Invalid response from API");
        }
        return [
            'names' => $names,
            'acronyms' => $acronyms
        ];
    }

    public function getAcronymById($id){
        $url = $this->rorBaseUrl . $this->organizationUrl . '?query.advanced=id:"' . str_replace(':', '\:', $id) . '"';
        $response = Http::get($url);
        if ($response->status() == 200) {
            foreach ($response['items'] as $item) {
                $name = null;
                foreach($item['names'] as $n){
                    if (in_array('acronym', $n['types'])){
                        $name = $n['value'];
                        break;
                    }
                }
                if ($name == null){
                    $name = '';
                }
                return $name;
            }
        }
        return '';
    }
}
