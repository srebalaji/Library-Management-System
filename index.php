<?php
require('db.php');
include('header.php');
?>
<div class="container">
  <div class="row">
    <div class="span12">
      Enter anyone of the field to search
      <form method="get" action="index.php" class="form-inline" >
        <input name="book_id" class="span5" type="text"  placeholder="Book Id" >
        <input name="title" class="span5" type="text"  placeholder="Title">
        <input name="author_name" class="span5" type="text"  placeholder="Author Name">
        <button type="submit" class="btn btn-primary">Search </button>
      </form>
    </div>
  </div>
<?php
if (isset($_GET['book_id']) && isset($_GET['title']) && isset($_GET['author_name']))
{
  $book_id = $_GET['book_id'];
  $title = $_GET['title'];
  $author_name = $_GET['author_name'];

  $search_query = "select a.book_id, a.title, b.authors_name, GROUP_CONCAT(c.branch_id) as branch_id, GROUP_CONCAT(d.branch_name) as branch_name, GROUP_CONCAT(c.no_of_copies) as no_of_copies, GROUP_CONCAT(ifnull((c.no_of_copies - f.idas),c.no_of_copies)) as avaliable from books a inner join (select book_id, GROUP_CONCAT(author_name) as authors_name FROM book_authors group by book_id) b on a.book_id = b.book_id left join book_copies c on a.book_id = c.book_id left join library_branch d on c.branch_id = d.branch_id left join book_loans e on c.book_id = e.book_id and c.branch_id = e.branch_id left join (select *,count(id)idas from book_loans where date_in is null group by branch_id, card_no) f on c.book_id = f.book_id and c.branch_id = f.branch_id where a.book_id like '%".$book_id."%' and a.title like '%".$title."%' and b.authors_name like '%".$author_name."%' group by a.book_id";
  $query = mysql_query($search_query);
  ?>
  
  <?php
  while ($fetch = mysql_fetch_assoc($query))
  {
?>
    <div class="panel panel-default">
     <div class="panel-body">
      <?php
      $book_id = $fetch['book_id'];
      ?>
        <h3><?php echo $fetch['title']?></h3>
        <h4> Book ID : <?php echo $book_id; ?></h4>
        <h5>Written By : <?php echo $fetch['authors_name']; ?></h5>
        <h4>Books Availability</h4>
        <?php
        $branch_ids = explode(",", $fetch['branch_id']);
        $branch_names = explode(",", $fetch['branch_name']);
        $no_of_copies = explode(",", $fetch['no_of_copies']);
        $avaliable_copies = explode(",", $fetch['avaliable']);
        ?>
        <table id="example1" class="table table-bordered table-striped tftable" border="1">
          <thead>
            <tr>
              <th>Branch ID</th>
              <th>Branch Location</th>
              <th>Total Copies</th>
              <th>Available Copies</th>
              <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php for($i=0; $i<count($branch_ids); $i++)
            {
              echo "<tr>";
              echo "<td>".$branch_ids[$i]."</td>";
              echo "<td>".$branch_names[$i]."</td>";
              echo "<td>".$no_of_copies[$i]."</td>";
              echo "<td>".$avaliable_copies[$i]."</td>";
              echo "<td><a class = 'btn btn-primary' href='check_out.php?bookid=".$book_id."&branchid=".$branch_ids[$i]."'>Check Out</a></td>";
              echo "</tr>";
            }
            ?>
         </tbody>
        </table>
        
     </div>
    </div>
<?php
  }
}
?>
</div>
</body>
</html>