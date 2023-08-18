<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductModel extends CI_Model {

    public function __construct(){

        $this->load->database();

    }

    public function save(){ 

        $json = file_get_contents('php://input');

        $data = json_decode($json);

        $field = array(

            'name'=>$data->name,

            'mrp'=>$data->mrp,

            'price'=>$data->price,

            'available'=>$data->available,

        );

        $id = $data->id;

        if($id == 0){

            $this->db->insert("products", $field);

            $id = $this->db->insert_id();

        }        else{ 

            $this->db->where("id", $id);

            $this->db->update("products", $field);

        }

    }

    public function lists(){

        $data = $this->db->get("products");

        return $data->result();

    }

    public function getbyid($id){

        $this->db->where("id", $id);

        $data = $this->db->get("products");

        return $data->result()[0];

    }

    public function delete($id){

        $this->db->where("id", $id);

        $this->db->delete("products");

    }

    public function update($id){

        $json = file_get_contents('php://input');

        $data = json_decode($json);

        $field = array(

            'name'=>$data->name,

            'mrp'=>$data->mrp,

            'price'=>$data->price,

            'available'=>$data->available,

        );

        $this->db->where("id", $id);

        $this->db->update("products", $field);

    }

}

