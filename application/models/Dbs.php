<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class Dbs extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function row_array($field, $value, $table)
    { 
        return $this->db->where($field, $value, TRUE)->get($table)->row_array();
    }

    function result($table)
    { 
        return $this->db->get($table)->result();
    }

    function result_key($field, $value, $table)
    { 
        return $this->db->where($field, $value, TRUE)->get($table)->result();
    }

    function chat($id, $id2)
    {
        return $this->db->select('c1.*, c1.reply as status_reply, c2.text as reply_msg, c2.photo as reply_img')->from('chat c1')->where("(c1._to = $id AND c1._from = $id2) OR (c1._to = $id2 AND c1._from = $id)")->join('chat c2', 'c1.reply = c2.id', 'left')->get()->result();
    }

    function login($name, $pass)
    { 
        $check = $this->db->where('username', $name, TRUE)->get('user')->row_array();
        return ($check && $pass == decrypt($check['password'])) ? $check : FALSE;
    }

    function insert($data, $table)
    {
        return $this->db->insert($table, $data);
    }

    function delete_all_chat($id, $id2)
    {
        //$id2 = yang menghapus
        //menghapus semua pesan (2 opsi)
        //jika yang menghapus satu orang (pilih data yang terakhir untuk menghindari kesalahpahaman karena terkadang ketika salah satu user menghapus semua pesan dan user satunya mengirimkan pesan baru maka akan membingungkan coding nya)
        $check_chat = $this->db->where("(_to = $id AND _from = $id2) OR (_to = $id2 AND _from = $id)")->get('chat')->row_array();
        //jika ada chat antara user yang bersangkutan
        if($check_chat)
        {   

            //jika salah satu user atau kedua-dua nya, sudah menghapus semua pesan
            $check_delete = $this->db->where("((_to = $id AND _from = $id2) OR (_to = $id2 AND _from = $id)) AND delete_all_by IS NOT NULL")->get('chat')->result();
            if($check_delete)
            {
                $delete_all_by_null     = [];
                $delete_all_by_not_null = [];
                $data_delete            = $this->db->where("(_to = $id AND _from = $id2) OR (_to = $id2 AND _from = $id)")->get('chat')->result();
                //jika hanya satu user yang menghapus semua pesan dan yang menghapus bukan user pertama
                foreach($data_delete as $dd)
                {
                    //jika pesan yang dihapus tidak sama dengan pesan yang dihapus oleh user pertama yang menghapus dan value delete_all_by tidak sama dengan NULL
                    // semua user menghapus pesan
                    if($dd->delete_all_by != $id2 && $dd->delete_all_by != NULL)
                    {
                        $delete_all_by_not_null[] = $dd->id;
                        $photo = explode(",", $dd->photo);
                        foreach($photo as $p)
                        {
                            if($p != null)
                            {
                                unlink("aset/photo/chat/" . $p);
                            }
                        }
                    }
                    else
                    {
                        $delete_all_by_null[] = $dd->id;
                    }
                }
                $all_id_not_null = implode(',', $delete_all_by_not_null);
                $all_id_null = implode(',', $delete_all_by_null);

                $data_update['delete_all_by'] = $id2;

                $delete  = ($all_id_not_null != NULL) ? $this->db->where("id IN($all_id_not_null)")->delete('chat') : FALSE;
                $update  = ($all_id_null != NULL) ? $this->db->where("id IN($all_id_null)")->update('chat', $data_update) : FALSE;
                return [$delete, $update];

            }
            //jika salah satu belum ada yang menghapus
            else
            {
                $data_delete = [ 'delete_all_by' => $id2 ];
                return $this->db->where("(_to = $id AND _from = $id2) OR (_to = $id2 AND _from = $id)")->update('chat', $data_delete);
            }
        }
        return false;
    }

    function delete_chat($id, $id_user)
    {
        $data_update = ['delete_by' => $id_user];
        return $this->db->where('id', $id, TRUE)->where("_from", $id_user, TRUE)->update('chat', $data_update);
    }

    function check_edit($id, $id_user)
    {
        return $this->db->where('id', $id, TRUE)->where('_from', $id_user, TRUE)->get('chat')->result();
    }

    function update($field, $value, $data, $table)
    {
        return $this->db->where($field, $value, TRUE)->update($table, $data);
    }
}