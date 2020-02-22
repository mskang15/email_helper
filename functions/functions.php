<?php
/**
 * Created by PhpStorm.
 * User: MoonSeokKang
 * Date: 2/21/20
 * Time: 1:24 PM
 */

/**
 * Output API response
 *
 * @param $result_array
 * @param $r
 * @param null $code
 * @param null $error
 */
function apiResponse($result_array, $r, $code = NULL, $error = NULL)
{
    $now = gmdate("D, d M Y H:i:s");

    if (!empty($result_array['filename'])) {
        $file_name = $result_array['filename'] . ".$r";
    } else {
        if (!empty($_SESSION['authid'])) {
            $file_name = "data_export_" . $_SESSION['authid'] . '_' . date("Y-m-d_h.m.s") . ".$r";
        } else {
            $file_name = "data_export_anon_" . date("Y-m-d_h.m.s") . ".$r";
        }
    }

    if (!empty($error)) {
        header("HTTP/1.1 $code $error", true);
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate", true);
        header("Pragma: no-cache", true);
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT", true);
    }

    if (empty($result_array)) {
        header("HTTP/1.1 204 No Content", true);
    }

    if (empty($r) || ($r == "json")) {
        if (isset($result_array['info'][0]['lastmodified'])) {
            $lme = strtotime($result_array['info'][0]['lastmodified']);
            $lm = date("D, d M Y H:i:s", $lme);
            $etag = md5($lm);
            header("Cache-Control: max-age=240, no-cache, private", true);
            header("Last-Modified: {$lm} GMT", true);
            header("Etag: $etag");
            if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lme ||
                @trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag
            ) {
                header("HTTP/1.1 304 Not Modified", true);
                exit;
            }
        } elseif (isset($result_array['info'][0]['etag']) || isset($result_array['etag'])) {
            $etag = isset($result_array['info'][0]['etag']) ? $result_array['info'][0]['etag'] : $result_array['etag'];
            header("Cache-Control: max-age=43200", true);
            header("Etag: $etag");
            if (@trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
                header("HTTP/1.1 304 Not Modified", true);
                exit;
            }
        } else {
            header("Cache-Control: max-age=0, no-store, no-cache, private", true);
        }

        header('Content-Type: application/json;charset=utf-8');
        if (isset($_GET['callback'])) {
            echo $_GET['callback'] . "(";
        }

        if (!empty($result_array)) {
            echo json_encode($result_array, JSON_UNESCAPED_UNICODE);
        }

        if (isset($_GET['callback'])) {
            echo ")";
        }
    } elseif ($r == "csv") {
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Pragma: no-cache"); //HTTP 1.0
        header("Last-Modified: {$now} GMT");

        // Force download
        if (empty($error)) {
            header("Content-Type: application/force-download;charset=utf-16le");
            header("Content-Type: application/octet-stream;charset=utf-16le");
            header("Content-Type: application/download;charset=utf-16le");

            // Disposition / encoding on response body
            header("Content-Disposition: attachment;filename={$file_name}");
            header("Content-Transfer-Encoding: binary");
        } else {
            header("Content-Type: text/csv;charset=utf-16le");
        }

        if (isset($result_array['info'])) {
            $csv_array = $result_array['info'];
        } elseif (isset($result_array['names'])) {
            $csv_array = $result_array['names'];
        } elseif (isset($result_array['flattxt'])) {
            if (is_array($result_array['flattxt'])) {
                $flat_text = implode("\n", $result_array['flattxt']);
            } else {
                $flat_text = $result_array['flattxt'];
            }
        } else {
            $csv_array = $result_array;
        }

        if (!empty($csv_array)) {
            echo chr(255) . chr(254) . mb_convert_encoding(array2csv($csv_array, "\t"), 'UTF-16LE', 'UTF-8');
        }

        if (!empty($flat_text)) {
            echo chr(255) . chr(254) . mb_convert_encoding($flat_text, 'UTF-16LE', 'UTF-8');
        }
    } elseif ($r == "html") {
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        if (isset($result_array['info'])) {
            $html_array = $result_array['info'];
        } elseif (isset($result_array['names'])) {
            $html_array = $result_array['names'];
        } else {
            $html_array = $result_array;
        }

        if (!empty($html_array)) {
            echo "<html><head><title>Reports: ".date("Y-m-d h.m.s")."</title>";
            echo "    <link href=\"https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css\" rel=\"stylesheet\" type=\"text/css\"/>";
            echo "    <script src=\"https://code.jquery.com/jquery-3.4.1.min.js\" integrity=\"sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=\" crossorigin=\"anonymous\"></script>";
            echo "    <script src=\"https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js\"></script>";
            echo "</head><body>";
            echo array2html($html_array);
            echo "<script>$(document).ready( function () { $('#reportTable').DataTable({\"scrollX\": true, \"pageLength\": 20, \"lengthMenu\": [[ 10, 20, 50, 75, 100, -1 ], [10, 20, 50, 75, 100, \"All\"]]});});</script></body></html>";
        }
    } elseif ($r == "txt") {
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // Force download
        if (!empty($result_array)) {
            if (isset($result_array['info'])) {
                $csv_array = $result_array['info'];
            } elseif (isset($result_array['names'])) {
                $csv_array = $result_array['names'];
            } elseif (isset($result_array['flattxt'])) {
                if (is_array($result_array['flattxt'])) {
                    $flat_text = implode("\n", $result_array['flattxt']);
                } else {
                    $flat_text = $result_array['flattxt'];
                }
            }

            if (!empty($csv_array)) {
                if (empty($error)) {
                    header("Content-Type: application/force-download;charset=utf-16le");
                    header("Content-Type: application/octet-stream;charset=utf-16le");
                    header("Content-Type: application/download;charset=utf-16le");

                    // Disposition / encoding on response body
                    header("Content-Disposition: attachment;filename={$file_name}");
                    header("Content-Transfer-Encoding: binary");
                } else {
                    header("Content-Type: text/csv;charset=utf-16le");
                }

                echo chr(255) . chr(254) . mb_convert_encoding(array2csv($csv_array, "\t"), 'UTF-16LE', 'UTF-8');
            } else {
                if (empty($error)) {
                    header("Content-Type: application/force-download;charset=utf-8");
                    header("Content-Type: application/octet-stream;charset=utf-8");
                    header("Content-Type: application/download;charset=utf-8");

                    // Disposition / encoding on response body
                    header("Content-Disposition: attachment;filename={$file_name}");
                    header("Content-Transfer-Encoding: binary");
                } else {
                    header("Content-Type: text/plain;charset=utf-8");
                }

                if (!empty($flat_text)) {
                    echo $flat_text;
                } else {
                    print_r($result_array);
                }
            }
        }
    } elseif($r == "bin") {
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // Force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // Disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$file_name}");
        header("Content-Transfer-Encoding: binary");
        echo $result_array;
    } elseif ($r == "dump") {
        header("Content-Type: text/plain;charset=utf-8");
        if (!empty($result_array)) {
            print_r($result_array);
        }
    } else {
        if (empty($error)) {
            header('HTTP/1.1 400 Bad Request', true);
        }

        header("Content-Type: text/plain;charset=utf-8");
        print "Return type is not supported.\n";
        print "Data Dump:\n";
        if (!empty($result_array)) {
            print_r($result_array);
        }
        exit;
    }
}

function validateEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return true;
}