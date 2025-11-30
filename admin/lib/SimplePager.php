<?php

class SimplePager {
    public $limit;      // Page size
    public $page;       // Current page
    public $item_count; // Total item count
    public $page_count; // Total page count
    public $result;     // Result set (array of records)
    public $count;      // Item count on the current page

    public function __construct($query, $params, $limit, $page) {
            global $_db;

            $this->limit = ctype_digit((string)$limit) ? max((int)$limit, 1) : 10;
            $this->page  = ctype_digit((string)$page)  ? max((int)$page, 1)   : 1;

            
            $count_query = $query;
            $count_query = preg_replace('/\s+ORDER\s+BY\s+.*$/i', '', $count_query); 
            $count_query = preg_replace('/^SELECT\s+.+?\s+FROM/i', 'SELECT COUNT(*) FROM', $count_query, 1);

            $stm = $_db->prepare($count_query);
            $stm->execute($params);
            $this->item_count = (int)$stm->fetchColumn();

            $this->page_count = ceil($this->item_count / $this->limit);
            $this->page = min($this->page, max(1, $this->page_count));

            $offset = ($this->page - 1) * $this->limit;
          
            $final_query = $query . " LIMIT $offset, {$this->limit}";
            $stm = $_db->prepare($final_query);
            $stm->execute($params);
            $this->result = $stm->fetchAll();

            $this->count = count($this->result);
    }

    public function html() {
        if ($this->page_count <= 1) return; 

        $prev = $this->page - 1;
        $next = $this->page + 1;

        echo '<div class="pager-container">';
        echo '<div class="pager">';

        
        if ($this->page > 1) {
            echo "<a href='?page=1' class='pager-btn'>First</a>";
            echo "<a href='?page=$prev' class='pager-btn'>Previous</a>";
        } else {
            echo "<span class='pager-btn disabled'>First</span>";
            echo "<span class='pager-btn disabled'>Previous</span>";
        }

        
        $start = max(1, $this->page - 3);
        $end   = min($this->page_count, $this->page + 3);

        if ($start > 1) echo "<span class='pager-dots'>...</span>";
        for ($i = $start; $i <= $end; $i++) {
            $active = $i == $this->page ? 'active' : '';
            echo "<a href='?page=$i' class='pager-btn $active'>$i</a>";
        }
        if ($end < $this->page_count) echo "<span class='pager-dots'>...</span>";

       
        if ($this->page < $this->page_count) {
            echo "<a href='?page=$next' class='pager-btn'>Next</a>";
            echo "<a href='?page={$this->page_count}' class='pager-btn'>Last</a>";
        } else {
            echo "<span class='pager-btn disabled'>Next</span>";
            echo "<span class='pager-btn disabled'>Last</span>";
        }

        echo '</div></div>';
    }
}