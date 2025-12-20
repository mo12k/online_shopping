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

        // Set [limit] and [page]
        $this->limit = ctype_digit($limit) ? max($limit, 1) : 10;
        $this->page = ctype_digit($page) ? max($page, 1) : 1;

        // Set [item count]
            $count_query = $query;
            $count_query = preg_replace('/\s+ORDER\s+BY\s+.*$/i', '', $count_query); 
            $count_query = preg_replace('/^SELECT\s+.+?\s+FROM/i', 'SELECT COUNT(*) FROM', $count_query, 1);

             $stm = $_db->prepare($count_query);
            $stm->execute($params);
            $this->item_count = (int)$stm->fetchColumn();

             $this->page_count = ceil($this->item_count / $this->limit);
            $this->page = min($this->page, max(1, $this->page_count));

        // Set [result]
        $offset = ($this->page - 1) * $this->limit;
          
            $final_query = $query . " LIMIT $offset, {$this->limit}";
            $stm = $_db->prepare($final_query);
            $stm->execute($params);
            $this->result = $stm->fetchAll();

        // Set [count]
        $this->count = count($this->result);
    }

    public function html($query = '') {
    if ($this->page_count <= 1) return;

    $query = $query ? '&' . ltrim($query, '&') : '';

    $prev = $this->page - 1;
    $next = $this->page + 1;

    echo '<div class="pager-container">';
    echo '<div class="pager">';

    // First / Previous
    if ($this->page > 1) {
        echo "<a href='?page=1$query' class='pager-btn'>First</a>";
        echo "<a href='?page=$prev$query' class='pager-btn'>Previous</a>";
    } else {
        echo "<span class='pager-btn disabled'>First</span>";
        echo "<span class='pager-btn disabled'>Previous</span>";
    }

    // Page numbers
    $start = max(1, $this->page - 3);
    $end   = min($this->page_count, $this->page + 3);

    if ($start > 1) echo "<span class='pager-dots'>...</span>";

    for ($i = $start; $i <= $end; $i++) {
        $active = $i == $this->page ? 'active' : '';
        echo "<a href='?page=$i$query' class='pager-btn $active'>$i</a>";
    }

    if ($end < $this->page_count) echo "<span class='pager-dots'>...</span>";

    // Next / Last
    if ($this->page < $this->page_count) {
        echo "<a href='?page=$next$query' class='pager-btn'>Next</a>";
        echo "<a href='?page={$this->page_count}$query' class='pager-btn'>Last</a>";
    } else {
        echo "<span class='pager-btn disabled'>Next</span>";
        echo "<span class='pager-btn disabled'>Last</span>";
    }

    echo '</div></div>';
}
}



class SimpleOPager {

    public int $limit;        
    public int $page;        
    public int $item_count;   
    public int $page_count;   
    public array $result;     
    public int $count;        

    public function __construct(
        string $list_sql,      
        string $count_sql,     
        array  $params,
        int    $limit,
        int    $page
    ) {
        global $_db;

      
        $this->limit = max((int)$limit, 1);
        $this->page  = max((int)$page, 1);

       
        $stm = $_db->prepare($count_sql);
        $stm->execute($params);
        $this->item_count = (int)$stm->fetchColumn();

        $this->page_count = max((int)ceil($this->item_count / $this->limit), 1);

        if ($this->page > $this->page_count) {
            $this->page = $this->page_count;
        }

       
        $offset = ($this->page - 1) * $this->limit;
        $final_sql = $list_sql . " LIMIT $offset, {$this->limit}";

        $stm = $_db->prepare($final_sql);
        $stm->execute($params);
        $this->result = $stm->fetchAll();

        $this->count = count($this->result);
    }

  
    public function html0(string $query = '') {
        if ($this->page_count <= 1) return;

        $query = $query ? '&' . ltrim($query, '&') : '';

        echo '<div class="pager-container"><div class="pager">';

        // First / Prev
        if ($this->page > 1) {
            echo "<a class='pager-btn' href='?page=1$query'>First</a>";
            echo "<a class='pager-btn' href='?page=" . ($this->page - 1) . "$query'>Previous</a>";
        } else {
            echo "<span class='pager-btn disabled'>First</span>";
            echo "<span class='pager-btn disabled'>Previous</span>";
        }

       
        $start = max(1, $this->page - 3);
        $end   = min($this->page_count, $this->page + 3);

        for ($i = $start; $i <= $end; $i++) {
            $active = $i === $this->page ? 'active' : '';
            echo "<a class='pager-btn $active' href='?page=$i$query'>$i</a>";
        }

       
        if ($this->page < $this->page_count) {
            echo "<a class='pager-btn' href='?page=" . ($this->page + 1) . "$query'>Next</a>";
            echo "<a class='pager-btn' href='?page={$this->page_count}$query'>Last</a>";
        } else {
            echo "<span class='pager-btn disabled'>Next</span>";
            echo "<span class='pager-btn disabled'>Last</span>";
        }

        echo '</div></div>';
    }
}
