<?php

namespace App\Traits;

trait FileTrait
{
  public static function addFileToCrm(
    int $folder_id,
    string $contract_name,
    mixed $file,
  ): array {
    $response = \App\Traits\BX::call('disk.folder.uploadfile', [
      'id' => //107643231232,
      $folder_id,
      'data' => [
        'NAME' => $contract_name,
      ],
      'fileContent' => base64_encode($file),
      'generateUniqueName' => true,
    ]);

    return isset($response['result'])
      ? [
        'status' => true,
        'data' => $response['result']['DOWNLOAD_URL'],
      ] : [
        'status' => false,
        'data' => $response['error_description'],
      ];
  }
}