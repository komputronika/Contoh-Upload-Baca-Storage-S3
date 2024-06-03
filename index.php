<?php

// Contoh script PHP untuk upload file ke server S3 compatible
//
// Referensi lengkap klik:
// https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/php_s3_code_examples.html
//
// Rekomendasi layanan Object Storage berbasis S3 di Indonesia
// BiznetGio, klik: https://www.biznetgio.com/
//
// VOUCHER DISKON:
//
// AFLITECLOUD Diskon 15 % NEO Lite
// AFNWPCLOUD Diskon 20% NEO WordPress
// AFNVCLOUD DIskon 10% NEO Virtual Compute
// AFNWHCLOUD Diskon 20% NEO Web Hosting
// AFNDHCLOUD DIskon 10% NEO Dedicated Hosting

require 'vendor/autoload.php';

// Silakan lengkapi dengan data yang didapat dari Biznet (provider S3)
define("BIZ_ENDPOINT", "********");
define("BIZ_ACCESS_KEY_ID", "********");
define("BIZ_ACCESS_KEY_SECRET", "*******");
define("BIZ_ACCESS_REGION", "****");
define("BIZ_BUCKET_NAME", "*****");

use Aws\S3\S3Client;

//=============================================================
// Contoh upload file S3
//=============================================================

// Buat object S3
$client = new S3Client([
    'version' => 'latest',
    'region' => BIZ_ACCESS_REGION,
    'endpoint' => "https://".BIZ_ENDPOINT,
    'credentials' => [
        'key'    => BIZ_ACCESS_KEY_ID,
        'secret' => BIZ_ACCESS_KEY_SECRET,
    ]
]);

// Tentukan file yang ingin di upload

// Nama folder dan file sebagai key di server 
$fileName = "demo/foto.png";    
// Path di harddisk
$path = "/home/me/MyFiles/foto.png";

// Proses upload
try {
    $result = $client->putObject([
        'Bucket' => BIZ_BUCKET_NAME,
        'Key'    => $fileName,
        'Body'   => fopen($path, 'r'),
        'ACL'    => 'public-read' // atau 'private'
    ]);
    $data = $result->toArray();
    $object_url = $data['ObjectURL'];
    print "<br>RESULT: ".$object_url;
} 
catch (Aws\S3\Exception\S3Exception $e) 
{
    echo "Ada kegagalan upload.\n";
}

//=============================================================
// Contoh membaca file dari S3
//=============================================================

try {
    $objects = $client->listObjects(['Bucket' => BIZ_BUCKET_NAME]);
  
    // Bila ingin membaca semua, lakukan loop 
    foreach ($objects['Contents'] as $object) 
    {
        $key = $object["Key"];
  
        $url = $client->getObjectUrl(BIZ_BUCKET_NAME, $key);
        $a = explode("/",$key);
        if (count($a)>1 && !empty($a[1]) ) 
        {
            // URL hanya dapat dibaca kalau ACL = 'public-read'
            echo "<img src='".$url."' width='100px'>". "<br>";

            // Bila ACL = 'private' 
            //
            // $file = $client->getObject([
            //     'Bucket' => BIZ_BUCKET_NAME,
            //     'Key' => $key,
            // ]);
            // $body = $file->get('Body');
            // $body->rewind();
            // 
            // Tambahkan http header sesuai extension
            // Kemudian, echo $body;
        }
    }
} 
catch (Aws\S3\Exception\S3Exception $e) 
{
    echo "Ada kesalahan dalam membaca file S3: " . $e->getMessage();
}
