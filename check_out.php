<?php
require('db.php');
include('header.php');
?>
<div class="container">
  <div class="row">
    <div class="span12">
      <form action="check_out.php" method="get">
        Book ID : <input type="text" name="book_id" value="<?php if(isset($_GET['bookid']))echo $_GET['bookid']?>">
        Branch ID : <input type="text" name="branch_id" value="<?php if(isset($_GET['branchid'])) echo $_GET['branchid']?>">
        Card Number : <input type="text" name="card_no" value="<?php if(isset($_GET['cardno'])) echo $_GET['cardno']?>">
        <button type="submit" class="btn btn-primary">Submit</button>
      </form>
    </div>
  </div>
<?php
if (isset($_GET['book_id']) && isset($_GET['branch_id']) && isset($_GET['card_no']))
{
  $card_no = $_GET['card_no'];
  $branch_id = $_GET['branch_id'];
  $book_id = $_GET['book_id'];

  $today_date = date("Y-m-d");
  $due_date =  date("Y-m-d", strtotime(date("Y-m-d", strtotime($today_date)) . " +2 week"));

  if (!checking_borrower_limit($card_no))
  die("You have reached your borrowing limit");
  if (!checking_book_availability($book_id,$branch_id,$card_no))
  die("There is no availability of this book in this branch");

  $check_out_query = "insert into book_loans(book_id, branch_id, card_no, date_out, due_date)values('$book_id', '$branch_id', '$card_no', '$today_date', '$due_date')";
  if (mysql_query($check_out_query))
  echo "Successfully checked out ";
  else
  echo "Error in Database";
}

function checking_borrower_limit($card_no)
{
  $checking_card_holder_limit_query = "select count(id) as total_count from book_loans where card_no = '$card_no' and date_in is null";
  if ($query = mysql_query($checking_card_holder_limit_query))
  {
    $row = mysql_fetch_assoc($query);
    if ($row['total_count'] >= 3)
      return false;
    return true;
  }
}

function checking_book_availability($book_id,$branch_id,$card_no)
{
  $checking_book_availability_query = "select (SELECT no_of_copies FROM book_copies a where a.book_id = '$book_id' and a.branch_id = '$branch_id') - (select count(id) from book_loans where book_id = '$book_id' and branch_id = '$branch_id' and date_in is null) as book_count";
  if ($query = mysql_query($checking_book_availability_query))
  {
    $row = mysql_fetch_assoc($query);
    if ($row['book_count'] <= 0)
      return false;
    return true;
  }
}
?>
</div>
</body>
</html>