<?php
require("db.php");
include("header.php");
?>
<div class="container">
  <div class="row">
    <div class="span12">
      Enter anyone of the field to search
      <form method="get" action="check_in.php" class="form-inline" >
        <input name="book_id" class="span5" type="text"  placeholder="Book Id" >
        <input name="card_no" class="span5" type="text"  placeholder="Card Number">
        <input name="borrower_name" class="span5" type="text"  placeholder="Borrower Name">
        <button type="submit" class="btn btn-primary">Search </button>
      </form>
    </div>
  </div>

<?php
if (isset($_GET['book_id']) && isset($_GET['card_no']) && isset($_GET['borrower_name']))
{
  $book_id = $_GET['book_id'];
  $card_no = $_GET['card_no'];
  $borrower_name = $_GET['borrower_name'];

  $search_query = "select a.id, a.book_id, a.card_no, a.date_out, a.due_date, a.branch_id, a.date_in, b.first_name, b.last_name, c.branch_name from book_loans a left join borrower b on a.card_no = b.card_no inner join library_branch c on a.branch_id = c.branch_id where a.date_in is null and a.book_id like '%".$book_id."%' and a.card_no like '%".$card_no."%' and (b.last_name like '%".$borrower_name."%' or b.first_name like '%".$borrower_name."%')";

  ?>
  <table id="example1" class="table table-bordered table-striped tftable" border="1">
    <thead>
      <tr>
        <th>Book ID</th>
        <th>Branch Name</th>
        <th>Card Number</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Date Out</th>
        <th>Due Date</th>
        <th>Action</th>
      </tr>
    </thead>
  <tbody>
  <?php
  if ($query = mysql_query($search_query))
  {
    while($fetch = mysql_fetch_assoc($query))
    {
      echo "<tr>";
      echo "<td>".$fetch['book_id']."</td>";
      echo "<td>".$fetch['branch_name']."</td>";
      echo "<td>".$fetch['card_no']."</td>";
      echo "<td>".$fetch['first_name']."</td>";
      echo "<td>".$fetch['last_name']."</td>";
      echo "<td>".$fetch['date_out']."</td>";
      echo "<td>".$fetch['due_date']."</td>";
      echo "<td><a class='btn btn-primary' href='check_in.php?book_loan_id=".$fetch['id']."'>Check In</a></td>";
      echo "</tr>";
    }
  }
}
?>
</tbody>
</table>

<?php
if (isset($_GET['book_loan_id']))
{
  $book_loan_id = $_GET['book_loan_id'];
  $query = mysql_query("select a.*,b.branch_name from book_loans a left join library_branch b on a.branch_id = b.branch_id where id = '$book_loan_id'");
  $rows = mysql_fetch_assoc($query);
?>
  
    <h3>Check In</h3>
    <h5>Book ID : <?php echo $rows['book_id']?></h5>
    <h5>Branch Name : <?php echo $rows['branch_name']?></h5>
    <h5>Card Number : <?php echo $rows['card_no']?></h5>
    <h5>Date Out : <?php echo $rows['date_out']?></h5>
    <h5>Due Date : <?php echo $rows['due_date']?></h5>
    <form method="post" action="check_in.php">
      Check In Date : <input type="text" class="datepicker" name="date_in">
      <input type="hidden" name="book_loan_id" value="<?php echo $_GET['book_loan_id']?>">
      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
  
<?php
}

if(isset($_POST['date_in']) && isset($_POST['book_loan_id']))
{
  $book_loan_id = $_POST['book_loan_id'];
  $date_in = $_POST['date_in'];
  $query = mysql_query("update book_loans set date_in = '$date_in' where id = '$book_loan_id'");
  if ($query)
    echo "Successfully Updated";
  else
    echo "Not Successful";
}
?>
</div>
<script>
$('.datepicker').datepicker({
    format: 'yyyy-mm-dd'
});
</script>
</body>
</html>
<?php

?>