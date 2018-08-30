<?php class BuildParentChild {

    private $data = array();
    public $rendered;

    public function __construct(&$Input)
    {
        foreach($Input as $Item)
        {
            $Item= (array) $Item;
            $this->data['items'][$Item['comment_id']] = $Item;
            $this->data['parents'][$Item['parent_id']][] = $Item['comment_id'];
            if(!isset($this->top_level) || $this->top_level > $Item['parent_id'])
            {
                $this->top_level = $Item['parent_id'];
            }
        }
        return $this;
    }

    public function build($id)
    {
        $return{$id} = array();
        foreach($this->data['parents'][$id] as $child)
        {
            $build = $this->data['items'][$child];
            if(isset($this->data['parents'][$child]))
            {
                $build['has_children'] = true;
                $build['children'] = $this->build($child);
            }
            else
            {
                $build['has_children'] = false;
            }
            $return{$id}[] = $build;
        }
        return (array) $return{$id};
    }

    public function render()
    {
        if(!isset($this->rendered) || !is_array($this->rendered))
        {
            $this->rendered = $this->build($this->top_level);
        }
        return $this->rendered;
    }
}
?>