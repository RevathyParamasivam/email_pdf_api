<?php
use Config\Services;
function isValidExtension($ext)
{
    $except = "png|gif|jpg|jpeg|pdf|docx|doc";
    if (preg_match('/(' . $except . ')/', $ext)) {
        return true;
    } else {
        return false;
    }

}

function isImage($ext)
{
    $except = "png|gif|jpg|jpeg";
    if (preg_match('/(' . $except . ')/', $ext)) {
        return true;
    } else {
        return false;
    }

}

function isDoc($ext)
{
    $except = "pdf|docx|doc";
    if (preg_match('/(' . $except . ')/', $ext)) {
        return true;
    } else {
        return false;
    }

}

function validatFileExtension($file, $allowedType = false)
{
    # code...
    foreach ($file['files'] as $img) {
        $ext = $img->getExtension();
        if (!$allowedType) {
            if (!isValidExtension($ext)) {
                return false;
            }

        } else if ($allowedType == 'IMAGE') {
            if (!isImage($ext)) {
                return false;
            }
        } else if ($allowedType == 'DOC') {
            if (!isDoc($ext)) {
                return false;
            }
        }
    }

    return true;
}

function uploadFile($img, $folderName = false)
{
    # code...
    if (!$img) {
        return false;
    }

    $newName = $img->getRandomName();
    if ($folderName) {
        $folder = $folderName;
    } else {
        $folder = 'uploads/';
    }

    //$newName;
    $security = Services::security();
    $fileName = $newName;
    $security->sanitizeFilename($newName);

    if ($img->isValid() && !$img->hasMoved()) {
        if ($img->move($folder, $newName)) {
            return $fileName;
        } else {
            return false;
        }

    }
}

function validateSize($file)
{

    foreach ($file['files'] as $img) {
        $size = $img->getSizeByUnit('mb');

        if ($size > 1) {
            return false;
        }

    }
    return true;
}

function getTempPath()
{
    $appConstant = new \Config\AppConstant();
    return $appConstant->TEM_PATH;
}
function pathExits($path)
{
    if (!file_exists($path)) {
        $old_umask = umask(0);
        mkdir($path, 0777, true);
        umask($old_umask);
        // if (mkdir($path, 777, true)) {
        //     chmod($path, 0777);
        // }

    }
}

function moveFile($from, $destination, $fileName)
{
    pathExits($from);
    pathExits($destination);
    $path;
    $to;
    if (endsWith($from, '/')) {
        $path = $from . $fileName;
    } else {
        $path = $from . '/' . $fileName;
    }

    if (endsWith($destination, '/')) {
        $to = $destination . $fileName;
    } else {
        $to = $destination . '/' . $fileName;
    }
    if (@rename($path, $to)) {
        chmod($to, 0777);
        @unlink($path);
        return true;
    } else {
        return false;
    }
}
function fileWrite($filePath, $data, $rFrom = false, $rTo = '')
{
    if ($rFrom !== false) {
        $data = str_replace($rFrom, $rTo, $data);
    }
    $folderPath = preg_replace('#\/[^/]*$#', '', $filePath);
    pathExits($folderPath);
    $file = fopen($filePath, "w") or die("Unable to open file!");
    fwrite($file, $data);
    fclose($file);
    chmod($filePath, 0777);
    // code...
}

function deleteImg($from, $fileName)
{
    if (!endsWith($from, '/')) {
        $from = $from . '/';
    }
    if ($fileName) {
        $path = $from . $fileName;
        @unlink($path);
    }
}
// Return the full path to this file
function getFilePath($path, $fileName)
{
    if (!endsWith($path, '/')) {
        $path = $path . '/';
    }
    return BASEURL . $path . $fileName;
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    return $length > 0 ? substr($haystack, -$length) === $needle : true;
}

function modelMoveFile($data, $va)
{
    $temp = getTempPath();
    foreach ($va as $key => $value) {
        if (isset($data[$key]) && !empty($data[$key])) {
            $epl        = '[~@!OLDIMAGE!@~]';
            $expData    = explode($epl, $data[$key]);
            $data[$key] = $expData[0];
           // echo $data[$key];
            moveFile($temp, $value, $data[$key]);
            if (isset($expData[1])) {
                deleteImg($value, $expData[1]);
            }
        }
    }
    return $data;
}

function modelFileHandler(&$data, $va)
{
    if (count($data) !== count($data, COUNT_RECURSIVE)) {
        foreach ($data as &$item) {
            $item = modelMoveFile($item, $va);
        }
    } else {
        $data = modelMoveFile($data, $va);
    }
    return $data;
}

function addImageRealPath(array &$data, $va)
{
    if (count($data) !== count($data, COUNT_RECURSIVE)) {
        foreach ($data as &$item) {
            $item = mapFilePath($item, $va);
        }
    } else {
        $data = mapFilePath($data, $va);
    }
    return $data;
}

function mapFilePath($data, $va)
{
    foreach ($va as $key => $value) {
        if (isset($data[$key])) {
            $data[$key . '_path'] = getFilePath($value, $data[$key]);
        }
    }
    return $data;
}

function getThumbnail($style = 'src')
{
    $data = $this->attributes['thumbnail'] ?:
    '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';

    if ($style == 'src') {
        return 'data:image/jpg;base64,' . $data;
    }

    return base64_decode($data);
}
