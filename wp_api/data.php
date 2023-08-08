<?php
include_once 'config/Database.php';
include_once 'class/Items.php';
include_once 'helper.php';

define("REQMETHOD", "Method Not Supported !");
class Data extends Helper
{
    public $route;

    public function __construct()
    {
        $URI = $_SERVER["REQUEST_URI"];
        $this->route = substr($URI, 8);
    }



    public static function read(): string
    {

        $database = new Database();
        $db = $database->getConnection();
        $items = new Items($db);

        $items->id = (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : '0';
        $perpage = (isset($_GET['number_of_records']) && $_GET['number_of_records']) ? $_GET['number_of_records'] : '3';

        $result = $items->read($_REQUEST['page'], $perpage);



        if ($result['d']->num_rows > 0) {
            $itemRecords = array();

            $itemRecords["page"] = $_REQUEST['page'];
            $itemRecords["total_pages"] = round($result['row_count'] / $perpage);
            $itemRecords["number_of_records"] = $perpage;
            $itemRecords["records"] = array();
            $data = [];

            while ($item = $result['d']->fetch_assoc()) {
                extract($item);
                $itemDetails = array(
                    "id" => $item['ID'],
                    "post_type" => $item['post_type'],
                    "post_status" => $item['post_status'],
                    "post_title" => $item['post_title'],
                    "post_name" => $item['post_name'],
                    "post_date" => $item['post_date'],
                    "post_modified" => $item['post_modified'],
                    "display_name" => $item['post_modified'],

                );
                array_push($itemRecords["records"], $itemDetails);
            }
            http_response_code(200);

            $data['status'] = 200;
            $data['reason'] = "Success";
            $data['data'] = $itemRecords;

            return json_encode($data);
        } else {
            http_response_code(404);
            return json_encode(
                array("message" => "No item found.")
            );
        }
    }

    public function update()
    {
        $database = new Database();
        $db = $database->getConnection();
        $items = new Items($db);


        $data = json_decode(file_get_contents("php://input"));



        if (
            !empty($data->id) && !empty($data->post_type) &&
            !empty($data->post_title)
        ) {

            $items->id = $data->id;
            $items->post_type = $data->post_type;
            $items->post_title = $data->post_title;
            $items->post_modified = date('Y-m-d H:i:s');



            if ($items->update()) {
                $items->id = (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : '0';
                $perpage = (isset($_GET['number_of_records']) && $_GET['number_of_records']) ? $_GET['number_of_records'] : '1';

                $result = $items->read($_REQUEST['page'] = 1, $perpage, $data->id);

                if ($result['d']->num_rows > 0) {
                    $itemRecords = array();

                    $itemRecords["page"] = 1;
                    $itemRecords["total_pages"] = round($result['row_count'] / $perpage);
                    $itemRecords["number_of_records"] = $perpage;
                    $itemRecords["records"] = array();
                    $data = [];

                    while ($item = $result['d']->fetch_assoc()) {
                        extract($item);
                        $itemDetails = array(
                            "id" => $item['ID'],
                            "post_type" => $item['post_type'],
                            "post_status" => $item['post_status'],
                            "post_title" => $item['post_title'],
                            "post_name" => $item['post_name'],
                            "post_date" => $item['post_date'],
                            "post_modified" => $item['post_modified'],
                            "display_name" => $item['post_modified'],

                        );
                        array_push($itemRecords["records"], $itemDetails);
                    }
                    http_response_code(200);

                    $data['status'] = 200;
                    $data['reason'] = "Success";
                    $data['data'] = $itemRecords;

                    http_response_code(200);
                    return json_encode($data);
                }

                //return json_encode(array("message" => "Post was updated."));
            } else {
                http_response_code(503);
                $data = [];
                $data['status'] = 503;
                $data['reason'] = "Failed";
                $data['message'] = "Unable to update Post.";

                return json_encode($data);
            }
        } else {
            http_response_code(400);
            return json_encode(array("message" => "Unable to update Post. Data is incomplete."));
        }
    }

    public function getByid()
    {
        $database = new Database();
        $db = $database->getConnection();
        $items = new Items($db);


        $data = json_decode(file_get_contents("php://input"));

        $perpage=1;
        $result = $items->getByid($data->id);

        if ($result['d']->num_rows > 0) {
            $itemRecords = array();

            $itemRecords["page"] = 1;
           
            $itemRecords["total_pages"] = round($result['row_count'] / $perpage);
            $itemRecords["number_of_records"] = $perpage;
            $itemRecords["records"] = array();
            $data = [];

            while ($item = $result['d']->fetch_assoc()) {
                extract($item);
                $itemDetails = array(
                    "id" => $item['ID'],
                    "post_type" => $item['post_type'],
                    "post_status" => $item['post_status'],
                    "post_title" => $item['post_title'],
                    "post_name" => $item['post_name'],
                    "post_date" => $item['post_date'],
                    "post_modified" => $item['post_modified'],
                    "display_name" => $item['post_modified'],

                );
                array_push($itemRecords["records"], $itemDetails);
            }
            http_response_code(200);

            $data['status'] = 200;
            $data['reason'] = "Success";
            $data['data'] = $itemRecords;

            http_response_code(200);
            return json_encode($data);
        } else {
            http_response_code(400);
            return json_encode(array("message" => "Unable to Find Post. Id is Invalid."));
        }
    }
}


$obj = new Data();
$status = $obj->check($obj->route);


if ($status == true) {

    if (isset($_REQUEST['page'])) {
        $reqpage = $_REQUEST['page'];
    } else {
        $reqpage = 1;
    }


    switch ($obj->route) {
        case "pages/{$reqpage}":

            echo Data::read();
            break;


        case "page/update":

            echo $obj->update();
            break;

        case "page":

            echo $obj->getByid();
            break;


        default:
            echo json_encode(REQMETHOD);
    }
} else {
    echo json_encode(REQMETHOD);
}
