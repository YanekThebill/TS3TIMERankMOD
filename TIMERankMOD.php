<?php
/**
 * @author YanekThebill
 * @copyright 2014
 */

//-----------------------------------------------------------------------------------------------------------------
// TEAMSPEAK SETTINGS
$botname = "TIMERankMOD"; //Bot Name
$TSaddress = "127.0.0.1"; // TS server Adrress
$TSqueryport = "10011"; //Ts server query port
$TSport = "9987"; //TS Port
$TSadminNickname = "serveradmin"; //TS Admin Nickname
$TSpassword = "pass"; // Ts Admin Pass
//-----------------------------------------------------------------------------------------------------------------
// RANK SETTINGS

$Rank1 = "88";  //Rank SG ID
$timetoget1 = "86400"; //24h  Time to get
$name1 = "Rank 1"; //Rank Name
$Rank2 = "89";
$timetoget2 = "604800"; // 7 days
$name2 = "Rank 2";
$Rank3 = "90";
$timetoget3 = "1209600"; //14 days
$name3 = "Rank 3";
$Rank4 = "91";
$timetoget4 = "1814400"; //21 days
$name4 = "Rank 4";
$Rank5 = "92";
$timetoget5 = "2678400"; // 31 days
$name5 = "Rank 5";
$Rank6 = "93";
$timetoget6 = "5184000"; //60 days
$name6 = "Rank 6";
$Rank7 = "94";
$timetoget7 = "7776000"; //90 days
$name7 = "Rank 7";
$Rank8 = "95";
$timetoget8 = "10368000"; //120 days
$name8 = "Rank 8";
$Rank9 = "96";
$timetoget9 = "15552000"; //180 days
$name9 = "Rank 9";
$Rank10 = "97";
$timetoget10 = "21600000"; //250 days
$name10 = "Rank 10";
$Rank11 = "98";
$timetoget11 = "31556926"; // 1 year
$name11 = "Rank 11";
$Rank12 = "99";
$timetoget12 = "47335389"; //1,5 year
$name12 = "Rank 12";
$Rank13 = "100";
$timetoget13 = "63113851"; // 2 years
$name13 = "Rank 13";
$Rank14 = "101";
$timetoget14 = "94670777"; // 3 years
$name14 = "Rank 14";
$Rank15 = "102";
$timetoget15 = "157784630"; //5 years
$name15 = "Rank 15";
//------------------------------------------------------------------------------------------------------------------

function time_elapsed($time)
{
    $bit = array(
        'y' => $time / 31556926 % 12,
        'w' => $time / 604800 % 52,
        'd' => $time / 86400 % 7,
        'h' => $time / 3600 % 24,
        'm' => $time / 60 % 60,
        's' => $time % 60);

    foreach ($bit as $k => $v)
        if ($v > 0)
            $ret[] = $v . $k;

    return join(' ', $ret);
}
// ERROR REPORT
ini_set('display_errors', 'On');
error_reporting(E_ALL);
$filename = "OnlineStatus.txt";
$totaltime = "TotalTime.txt";
set_time_limit(0);
date_default_timezone_set('Europe/Warsaw');
require_once dirname(__file__) .
    "/../TIMERankMOD/ts3php/libraries/TeamSpeak3/TeamSpeak3.php";


try
{
    $ts3_VirtualServer = TeamSpeak3::factory("serverquery://$TSadminNickname:$TSpassword@$TSaddress:$TSqueryport/?server_port=$TSport&nickname=$botname");


}
catch (TeamSpeak3_Exception $e)
{
    // print the error message returned by the server
    echo "Error " . $e->getCode() . ": " . $e->getMessage();
}

$arr_ClientList = $ts3_VirtualServer->clientList();
$TSuserdata = array();

foreach ($arr_ClientList as $TSClid => $TSArray)
{
    if ($TSArray->client_unique_identifier->toString() == "serveradmin")
    {
    } elseif ($TSArray->client_unique_identifier->toString() == "ServerQuery")
    {
    } else
    {
        array_push($TSuserdata, array(

            "TsNICK" => $TSArray->client_nickname->toString(),
            "TsDBID" => $TSArray->client_database_id,
            "TsUID" => $TSArray->client_unique_identifier->toString(),
            "TsSERVGIDs" => $TSArray->client_servergroups,
            "TsCHANGIDs" => $TSArray->client_channel_group_id,
            "TsCLIENTID" => $TSArray->clid,
            "TsCONTIME" => $TSArray->client_lastconnected,
            "TsIDLTIME" => $TSArray->client_idle_time,
            "TsCLIENTCHANID" => $TSArray->cid));

    }
}

foreach ($TSuserdata as $timeonline)
{
    $searchfor = "" . $timeonline["TsUID"] . "";
    $file = file_get_contents($filename);

    if (strpos($file, $searchfor) == false)
    {
        $timecon = $timeonline["TsCONTIME"];
        $data = "" . $timeonline["TsUID"] . ",$timecon\n";
        file_put_contents($filename, $data, FILE_APPEND | LOCK_EX);
    }
}


$fh = fopen($filename, 'r');
$members = array();
$onlinedata = array();


$i = 0;
while (!feof($fh))
{
    $huj = fgets($fh);
    $huj = trim($huj);
    $members[] = $huj;

}
fclose($fh);


foreach ($members as $Mem)
{
    $MemS = explode(",", $Mem);
    if (!empty($MemS[0]))
    {
        $a = ($MemS[0]);
        $b = ($MemS[1]);
        array_push($onlinedata, array("TsUID" => $a, "TsCONTIME" => $b));
    }
}

$tt = fopen($totaltime, 'r');
$members2 = array();
$totalonlinedata = array();


$i = 0;
while (!feof($tt))
{
    $chuj = fgets($tt);
    $chuj = trim($chuj);
    $members2[] = $chuj;

}
fclose($tt);


foreach ($members2 as $Mem2)
{
    $MemS2 = explode(",", $Mem2);
    if (!empty($MemS2[0]))
    {
        $a = ($MemS2[0]);
        $b = ($MemS2[1]);
        $time = $b;
        $c = time_elapsed($time);
        array_push($totalonlinedata, array(
            "TsUID" => $a,
            "TsTOTALONLINE" => $b,
            "TsTotalTime" => $c));
    }
}


$uilist = "";
foreach ($TSuserdata as $tsuid)
{
    $ui = $tsuid["TsUID"];
    $uilist = $uilist . $ui . ",";
}

foreach ($onlinedata as $ondat)
{
    if (!in_array($ondat["TsUID"], explode(',', $uilist)))
    {
        $file2 = file_get_contents($totaltime);
        $searchfor2 = $ondat["TsUID"];
        if (strpos($file2, $searchfor2) == false)
        {
            $sesiontime = time() - $ondat["TsCONTIME"];
            if ($sesiontime > 0)
            {
                $data = "" . $ondat["TsUID"] . ",$sesiontime\n";
                file_put_contents($totaltime, $data, FILE_APPEND | LOCK_EX);
            }
            $deleteEntry = "" . $ondat["TsUID"] . "," . $ondat["TsCONTIME"] . "";
            $file = file_get_contents($filename);
            $file = rtrim(str_replace("$deleteEntry", '', $file));
            $file = file_put_contents($filename, $file);
            file_put_contents($filename, "\n", FILE_APPEND | LOCK_EX);

        } elseif (strpos($file2, $searchfor2) !== false)
        {
            foreach ($totalonlinedata as $tsdat)
            {
                if ($ondat["TsUID"] == $tsdat["TsUID"])
                {
                    $sesiontime = time() - $ondat["TsCONTIME"];
                    $totalTime = $sesiontime + $tsdat["TsTOTALONLINE"];
                    if ($totalTime > 0)
                    {
                        $delEntry = "" . $tsdat["TsUID"] . "," . $tsdat["TsTOTALONLINE"] . "";
                        $dataentry = "" . $ondat["TsUID"] . ",$totalTime";
                        $file2 = file_get_contents($totaltime);
                        $file2 = rtrim(str_replace("$delEntry", "$dataentry", $file2));
                        $file2 = file_put_contents($totaltime, $file2);
                        file_put_contents($totaltime, "\n", FILE_APPEND | LOCK_EX);
                    }
                    $deleteEntry = "" . $ondat["TsUID"] . "," . $ondat["TsCONTIME"] . "";
                    $file = file_get_contents($filename);
                    $file = rtrim(str_replace("$deleteEntry", '', $file));
                    $file = file_put_contents($filename, $file);
                    file_put_contents($filename, "\n", FILE_APPEND | LOCK_EX);
                }
            }
        }
    }
}

foreach ($TSuserdata as $key => $s1)
{
    foreach ($totalonlinedata as $tr)
    {
        if ($s1["TsUID"] == $tr["TsUID"])
        {
            foreach ($tr as $k => $c)
            {
                $TSuserdata[$key][$k] = $c;
            }
            $find = 1;
            break;
        }
    }
}

foreach ($TSuserdata as $torank)
{
    if (array_key_exists("TsTOTALONLINE", $torank))
    {
        if ($torank["TsTOTALONLINE"] >= $timetoget15)
        {
            if (!in_array($Rank15, explode(',', $torank["TsSERVGIDs"])))
            {
                if (in_array($Rank14, explode(',', $torank["TsSERVGIDs"])))
                {
                    $ts3_VirtualServer->serverGroupClientDel($Rank14, ($torank["TsDBID"]));
                }
                $ts3_VirtualServer->serverGroupClientAdd($Rank15, ($torank["TsDBID"]));
                $ts3_VirtualServer->clientGetByDbid($torank["TsDBID"])->message("[b]\nYour all time online is: " .
                    $torank["TsTotalTime"] . "\n You get promoted to: $name15 [/b]");
            }
        } elseif ($torank["TsTOTALONLINE"] >= $timetoget14)
        {
            if (!in_array($Rank14, explode(',', $torank["TsSERVGIDs"])))
            {
                if (in_array($Rank13, explode(',', $torank["TsSERVGIDs"])))
                {
                    $ts3_VirtualServer->serverGroupClientDel($Rank13, ($torank["TsDBID"]));
                }
                $ts3_VirtualServer->serverGroupClientAdd($Rank14, ($torank["TsDBID"]));
                $ts3_VirtualServer->clientGetByDbid($torank["TsDBID"])->message("[b]\nYour all time online is: " .
                    $torank["TsTotalTime"] . "\n You get promoted to: $name14 [/b]");
            }
        } elseif ($torank["TsTOTALONLINE"] >= $timetoget13)
        {
            if (!in_array($Rank13, explode(',', $torank["TsSERVGIDs"])))
            {
                if (in_array($Rank12, explode(',', $torank["TsSERVGIDs"])))
                {
                    $ts3_VirtualServer->serverGroupClientDel($Rank12, ($torank["TsDBID"]));
                }
                $ts3_VirtualServer->serverGroupClientAdd($Rank13, ($torank["TsDBID"]));
                $ts3_VirtualServer->clientGetByDbid($torank["TsDBID"])->message("[b]\nYour all time online is: " .
                    $torank["TsTotalTime"] . "\n You get promoted to: $name13 [/b]");
            }
        } elseif ($torank["TsTOTALONLINE"] >= $timetoget12)
        {
            if (!in_array($Rank12, explode(',', $torank["TsSERVGIDs"])))
            {
                if (in_array($Rank11, explode(',', $torank["TsSERVGIDs"])))
                {
                    $ts3_VirtualServer->serverGroupClientDel($Rank11, ($torank["TsDBID"]));
                }
                $ts3_VirtualServer->serverGroupClientAdd($Rank12, ($torank["TsDBID"]));
                $ts3_VirtualServer->clientGetByDbid($torank["TsDBID"])->message("[b]\nYour all time online is: " .
                    $torank["TsTotalTime"] . "\n You get promoted to: $name12 [/b]");
            }
        } elseif ($torank["TsTOTALONLINE"] >= $timetoget11)
        {
            if (!in_array($Rank11, explode(',', $torank["TsSERVGIDs"])))
            {
                if (in_array($Rank10, explode(',', $torank["TsSERVGIDs"])))
                {
                    $ts3_VirtualServer->serverGroupClientDel($Rank10, ($torank["TsDBID"]));
                }
                $ts3_VirtualServer->serverGroupClientAdd($Rank11, ($torank["TsDBID"]));
                $ts3_VirtualServer->clientGetByDbid($torank["TsDBID"])->message("[b]\nYour all time online is: " .
                    $torank["TsTotalTime"] . "\n You get promoted to: $name11 [/b]");
            }
        } elseif ($torank["TsTOTALONLINE"] >= $timetoget10)
        {
            if (!in_array($Rank10, explode(',', $torank["TsSERVGIDs"])))
            {
                if (in_array($Rank9, explode(',', $torank["TsSERVGIDs"])))
                {
                    $ts3_VirtualServer->serverGroupClientDel($Rank9, ($torank["TsDBID"]));
                }
                $ts3_VirtualServer->serverGroupClientAdd($Rank10, ($torank["TsDBID"]));
                $ts3_VirtualServer->clientGetByDbid($torank["TsDBID"])->message("[b]\nYour all time online is: " .
                    $torank["TsTotalTime"] . "\n You get promoted to: $name10 [/b]");
            }
        } elseif ($torank["TsTOTALONLINE"] >= $timetoget9)
        {
            if (!in_array($Rank9, explode(',', $torank["TsSERVGIDs"])))
            {
                if (in_array($Rank8, explode(',', $torank["TsSERVGIDs"])))
                {
                    $ts3_VirtualServer->serverGroupClientDel($Rank8, ($torank["TsDBID"]));
                }
                $ts3_VirtualServer->serverGroupClientAdd($Rank9, ($torank["TsDBID"]));
                $ts3_VirtualServer->clientGetByDbid($torank["TsDBID"])->message("[b]\nYour all time online is: " .
                    $torank["TsTotalTime"] . "\n You get promoted to: $name9 [/b]");
            }
        } elseif ($torank["TsTOTALONLINE"] >= $timetoget8)
        {
            if (!in_array($Rank8, explode(',', $torank["TsSERVGIDs"])))
            {
                if (in_array($Rank7, explode(',', $torank["TsSERVGIDs"])))
                {
                    $ts3_VirtualServer->serverGroupClientDel($Rank7, ($torank["TsDBID"]));
                }
                $ts3_VirtualServer->serverGroupClientAdd($Rank8, ($torank["TsDBID"]));
                $ts3_VirtualServer->clientGetByDbid($torank["TsDBID"])->message("[b]\nYour all time online is: " .
                    $torank["TsTotalTime"] . "\n You get promoted to: $name8 [/b]");
            }
        } elseif ($torank["TsTOTALONLINE"] >= $timetoget7)
        {
            if (!in_array($Rank7, explode(',', $torank["TsSERVGIDs"])))
            {
                if (in_array($Rank6, explode(',', $torank["TsSERVGIDs"])))
                {
                    $ts3_VirtualServer->serverGroupClientDel($Rank6, ($torank["TsDBID"]));
                }
                $ts3_VirtualServer->serverGroupClientAdd($Rank7, ($torank["TsDBID"]));
                $ts3_VirtualServer->clientGetByDbid($torank["TsDBID"])->message("[b]\nYour all time online is: " .
                    $torank["TsTotalTime"] . "\n You get promoted to: $name7 [/b]");
            }
        } elseif ($torank["TsTOTALONLINE"] >= $timetoget6)
        {
            if (!in_array($Rank6, explode(',', $torank["TsSERVGIDs"])))
            {
                if (in_array($Rank5, explode(',', $torank["TsSERVGIDs"])))
                {
                    $ts3_VirtualServer->serverGroupClientDel($Rank5, ($torank["TsDBID"]));
                }
                $ts3_VirtualServer->serverGroupClientAdd($Rank6, ($torank["TsDBID"]));
                $ts3_VirtualServer->clientGetByDbid($torank["TsDBID"])->message("[b]\nYour all time online is: " .
                    $torank["TsTotalTime"] . "\n You get promoted to: $name6 [/b]");
            }
        } elseif ($torank["TsTOTALONLINE"] >= $timetoget5)
        {
            if (!in_array($Rank5, explode(',', $torank["TsSERVGIDs"])))
            {
                if (in_array($Rank4, explode(',', $torank["TsSERVGIDs"])))
                {
                    $ts3_VirtualServer->serverGroupClientDel($Rank4, ($torank["TsDBID"]));
                }
                $ts3_VirtualServer->serverGroupClientAdd($Rank5, ($torank["TsDBID"]));
                $ts3_VirtualServer->clientGetByDbid($torank["TsDBID"])->message("[b]\nYour all time online is: " .
                    $torank["TsTotalTime"] . "\n You get promoted to: $name5 [/b]");
            }
        } elseif ($torank["TsTOTALONLINE"] >= $timetoget4)
        {
            if (!in_array($Rank4, explode(',', $torank["TsSERVGIDs"])))
            {
                if (in_array($Rank3, explode(',', $torank["TsSERVGIDs"])))
                {
                    $ts3_VirtualServer->serverGroupClientDel($Rank3, ($torank["TsDBID"]));
                }
                $ts3_VirtualServer->serverGroupClientAdd($Rank4, ($torank["TsDBID"]));
                $ts3_VirtualServer->clientGetByDbid($torank["TsDBID"])->message("[b]\nYour all time online is: " .
                    $torank["TsTotalTime"] . "\n You get promoted to: $name4 [/b]");
            }
        } elseif ($torank["TsTOTALONLINE"] >= $timetoget3)
        {
            if (!in_array($Rank3, explode(',', $torank["TsSERVGIDs"])))
            {
                if (in_array($Rank2, explode(',', $torank["TsSERVGIDs"])))
                {
                    $ts3_VirtualServer->serverGroupClientDel($Rank2, ($torank["TsDBID"]));
                }
                $ts3_VirtualServer->serverGroupClientAdd($Rank3, ($torank["TsDBID"]));
                $ts3_VirtualServer->clientGetByDbid($torank["TsDBID"])->message("[b]\nYour all time online is: " .
                    $torank["TsTotalTime"] . "\n You get promoted to: $name3 [/b]");
            }
        } elseif ($torank["TsTOTALONLINE"] >= $timetoget2)
        {
            if (!in_array($Rank2, explode(',', $torank["TsSERVGIDs"])))
            {
                if (in_array($Rank1, explode(',', $torank["TsSERVGIDs"])))
                {
                    $ts3_VirtualServer->serverGroupClientDel($Rank1, ($torank["TsDBID"]));
                }
                $ts3_VirtualServer->serverGroupClientAdd($Rank2, ($torank["TsDBID"]));
                $ts3_VirtualServer->clientGetByDbid($torank["TsDBID"])->message("[b]\nYour all time online is: " .
                    $torank["TsTotalTime"] . "\n You get promoted to: $name2 [/b]");
            }
        } elseif ($torank["TsTOTALONLINE"] >= $timetoget1)
        {
            if (!in_array($Rank1, explode(',', $torank["TsSERVGIDs"])))
            {

                $ts3_VirtualServer->serverGroupClientAdd($Rank1, ($torank["TsDBID"]));
                $ts3_VirtualServer->clientGetByDbid($torank["TsDBID"])->message("[b]\nYour all time online is: " .
                    $torank["TsTotalTime"] . "\n You get promoted to: $name1 [/b]");
            }
        }
    }
}
?>
