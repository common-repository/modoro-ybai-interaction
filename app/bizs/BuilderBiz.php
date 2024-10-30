<?php


namespace YBAI\Bizs;

class BuilderBiz
{
    function __construct()
    {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'ybai_order_form';
        $this->db =& $wpdb;
    }

    function save($item){
        $meta_type= isset($item['meta_type']) ? $item['meta_type'] : '';
        $meta_name= isset($item['meta_name']) ? $item['meta_name'] : '';
        $meta_value= isset($item['meta_value']) ? $item['meta_value'] : '';
        $show_= '';
        $required_='';
        for($i=0;$i<=4;$i++) {
            if ($item['show_'.$i]=='on') 
                $show_=$show_."1;"; else $show_=$show_."0;";
            if ($item['required_'.$i]=='on') 
                $required_=$required_."1;"; else $required_=$required_."0;";
            unset($item['show_'.$i]);
            unset($item['required_'.$i]);
        }
        $item['show_']=$show_;
        $item['require_']=$required_;
        $key_over=1;

        if ($meta_name)
        foreach ($meta_name as $key => $value) {
            if(($item['meta_show_'.$key]==null)&&($item['meta_required_'.$key]==null)){
                while($item['meta_show_'.$key_over]==null){
                    $key_over++;
                }
                $item['meta_show_'.$key]=$item['meta_show_'.$key_over];
                $item['meta_required_'.$key]=$item['meta_required_'.$key_over];
            } 
            $key_over++;
        }
        if ($meta_name)
        foreach ($meta_name as $key => $value) {  
            if($item['meta_show_'.$key]=='on') $meta_show="1"; else $meta_show="0";
            if($item['meta_required_'.$key]=='on') $meta_required="1"; else $meta_required="0";
            $meta_data[] = ["name"=>$meta_name[$key],"type"=>$meta_type[$key],"value"=>$meta_value[$key],"show"=> $meta_show,"required"=> $meta_required];
        }
        if(($meta_data == null)&&($meta_name!== null))
        {
            if(isset($item['meta_show_0'])=='on') $meta_show="1"; else $meta_show="0";
            if(isset($item['meta_required_0'])=='on') $meta_required="1"; else $meta_required="0";
            $meta_data[]=["name"=>$meta_name,"type"=>$meta_type,"value"=>$meta_value,"show"=>$meta_show,"required"=>$meta_required];
            unset($item['meta_show_0']);
            unset($item['meta_required_0']);
        }
         for($i=0;$i<=$key_over;$i++){
            unset($item['meta_show_'.$i]);
            unset($item['meta_required_'.$i]);
        }
        $item['meta_data'] = json_encode($meta_data);
        // $item['infusion_tags'] = implode(',',$item['infusion_tags']);

        if (is_array($item['infusion_tags'])) {
            $tags = '';
            foreach ($item['infusion_tags'] as $tag) {
                $tags .= $tag . ',';
            }
            $tags = rtrim($tags, ',');
            $item['infusion_tags'] = $tags;
        }

        unset($item['meta_type']);
        unset($item['meta_name']);
        unset($item['meta_value']);
        unset($item['show']);
        unset($item['required']);
        unset($item['name']);
        unset($item['value']);
        unset($item['index']);
        unset($item['is_create']);

        $this->db->replace($this->table_name,$item);

        if($this->db->insert_id){
            $item['id'] = $this->db->insert_id;
            $item['shortcode'] = '[ybai-order id = "'.$item['id'].'"]';
            $item['success'] = str_replace("\\","",$item['success']);
            $this->db->replace($this->table_name,$item);
        }
    }

    function get_shortcode($id){
        $sql = 'SELECT * FROM '.$this->table_name. ' WHERE id = '.$id;
        $result = $this->db->get_row($sql);

        return $result;

    }

    function delete($id){
        if(is_int($id)){
            $sql = 'DELETE FROM '.$this->table_name. ' WHERE id = '.$id;
            $this->db->query($sql);
            return true;
        }
        return false;
    }

    function get_shortcodes(){
        $sql = 'SELECT * FROM ' . $this->table_name. ' ORDER BY id DESC';
        $result = $this->db->get_results($sql);

        return $result;
    }

    function get_lastid(){
        $sql = 'SELECT * FROM ' . $this->table_name. ' ORDER BY id DESC LIMIT 1';
        $result = $this->db->get_results($sql);

        return $result;
    }
}