<?php
namespace Application;

class Page {
    public function list($items) {
        http_response_code(200);
        echo json_encode($items);
    }

    public function item($item = false) {
        http_response_code(200);
        if ($item) {
            echo json_encode($item);
        }
    }

    public function notFound() {
        http_response_code(404);
        echo json_encode(["error" => "Not found"]);
    }

    public function badRequest() {
        http_response_code(400);
        echo json_encode(["error" => "Bad request"]);
    }

    // THE router
    public function route($method, $uri, $mail) {
        
        $parts = explode('/', trim($uri, '/'));

        
        if ($parts[0] === 'api' && $parts[1] === 'mail') {
            $id = $parts[2] ?? null; 

            switch ($method) {
                case 'GET':
                    if ($id) {
                        $item = $mail->getMailById($id);
                        if ($item) $this->item($item);
                        else $this->notFound();
                    } else {
                        $this->list($mail->getAllMails());
                    }
                    break;

                case 'POST':
                    $json = file_get_contents("php://input");
                    $data = json_decode($json, true);
                    if (isset($data['subject'], $data['body'])) {
                        $this->item($mail->createMail($data['subject'], $data['body']));
                    } else {
                        $this->badRequest();
                    }
                    break;

                case 'PUT':
                    if ($id) {
                        $json = file_get_contents("php://input");
                        $data = json_decode($json, true);
                        if (isset($data['subject'], $data['body'])) {
                            $mail->updateMail($id, $data['subject'], $data['body']);
                            $this->item($mail->getMailById($id));
                        } else {
                            $this->badRequest();
                        }
                    } else {
                        $this->badRequest();
                    }
                    break;

                case 'DELETE':
                    if ($id) {
                        $mail->deleteMail($id);
                        $this->item(["deleted_id" => $id]);
                    } else {
                        $this->badRequest();
                    }
                    break;

                default:
                    $this->badRequest();
            }
        } else {
            $this->notFound();
        }
    }
}
