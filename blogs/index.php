<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/config.inc.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/static/conn.php"); ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/lib/profile.php"); ?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $config['pr_title']; ?></title>
        <link rel="stylesheet" href="/static/css/required.css"> 
        <style>
            .customtopLeft {
                float: left;
                width: calc( 22% - 20px );
                padding: 10px;
            }

            .customtopRight {
                float: right;
                width: calc( 78% - 20px );
                padding: 10px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/header.php"); ?>
            <br>
            <div class="padding">
                <div class="customtopLeft">  
                    <div class="sideblog">
                        <h3>My Controls</h3>
                        <ul>
                            <li><a href="" class="man">Blog Home</a></li>
                            <li><a href="" class="man">My Subscriptions</a></li>
                            <li><a href="" class="man">My Readers</a></li>
                            <li class="last"><a href="" class="man">My Preferred List</a></li>
                        </ul>
                    </div><br>
                    <div class="sideblog">
                        <h3>Top 3 Bloggers</h3>
                        <ul>
                            <?php
                                $blogTop = array();
                                $stmt = $conn->prepare("SELECT * FROM blogs");
                                $stmt->execute();
                                $result = $stmt->get_result();
                                while($row = $result->fetch_assoc()) {
                                    if(!isset($blogsTop[$row['author']])) {
                                        $blogsTop[$row['author']] = 1;
                                    } else {
                                        $blogsTop[$row['author']]++;
                                    }   
                                }
                                $blogsTopBackup = $blogsTop;
                                rsort($blogsTop);
                                $top3 = array_slice($blogsTop, 0, 3);
                                foreach ($top3 as $key => $val) {?>
                                    <li><a href="" class="man"><?php echo $val; ?> : <?php $keysquared = array_search($val, $blogsTopBackup); echo $keysquared; ?></a></li> 
                                <?php } ?>
                        </ul>
                    </div>
                </div>
                
                <div class="customtopRight">  
                    <div class="splashBlue">
                        This doesn't really look that good... I'll try to make it look better later. <br><br><a href="new.php"><button>New Blog Post</button></a>
                    </div><br>
                    
                    <div class="login">
                        <div class="loginTopbar">
                            <b>All Blogs</b>
                        </div>
                        <div class="padding">
                            <?php
                                $total_pages = $conn->query('SELECT COUNT(*) FROM blogs WHERE visiblity = "Visible"')->fetch_row()[0]; 
                                $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
                                $num_results_on_page = 16;

                                $stmt = $conn->prepare("SELECT * FROM blogs WHERE visiblity = 'Visible' ORDER BY id DESC LIMIT ?,?");
                                $calc_page = ($page - 1) * $num_results_on_page;
                                $stmt->bind_param('ii', $calc_page, $num_results_on_page);
                                $stmt->execute();
                                $result = $stmt->get_result(); ?>
                            <div class="splashBlue">
                            <center>
                            <?php if (ceil($total_pages / $num_results_on_page) > 0): ?>
                                <?php if ($page > 1): ?>
                                <a href="index.php?page=<?php echo $page-1 ?>">Prev</a>
                                <?php endif; ?>

                                <?php if ($page > 3): ?>
                                <a href="index.php?page=1">1</a>
                                ...
                                <?php endif; ?>

                                <?php if ($page-2 > 0): ?><a href="index.php?page=<?php echo $page-2 ?>"><?php echo $page-2 ?></a><?php endif; ?>
                                <?php if ($page-1 > 0): ?><a href="index.php?page=<?php echo $page-1 ?>"><?php echo $page-1 ?></a><?php endif; ?>

                                <a href="index.php?page=<?php echo $page ?>"><?php echo $page ?></a>

                                <?php if ($page+1 < ceil($total_pages / $num_results_on_page)+1): ?><a href="index.php?page=<?php echo $page+1 ?>"><?php echo $page+1 ?></a></li><?php endif; ?>
                                <?php if ($page+2 < ceil($total_pages / $num_results_on_page)+1): ?><a href="index.php?page=<?php echo $page+2 ?>"><?php echo $page+2 ?></a></li><?php endif; ?>

                                <?php if ($page < ceil($total_pages / $num_results_on_page)-2): ?>
                                ...
                                <a href="index.php?page=<?php echo ceil($total_pages / $num_results_on_page) ?>"><?php echo ceil($total_pages / $num_results_on_page) ?></a>
                                <?php endif; ?>

                                <?php if ($page < ceil($total_pages / $num_results_on_page)): ?>
                                <a href="index.php?page=<?php echo $page+1 ?>">Next</a>
                                <?php endif; ?>
                            <?php endif; ?>
                                    <br>
                                    <form method="get" action="/users.php">
                                        <select name="searchmethod">
                                            <option value="new">Newest</option>
                                            <option value="old">Oldest</option>
                                            <option value="alph">Alphabetical</option>
                                        </select>
                                        <input type="submit" value="Go"> (Does not work yet)
                                    </form> 
                                </center>
                            </div>
                            <?php
                                while($row = $result->fetch_assoc()) { 
                            ?>
                                <div class="blog">
                                    <img style="float: left;height: 4em; width: 4em;" src="/dynamic/pfp/<?php echo getPFPFromUser($row['author'], $conn); ?>">
                                    <span id="blogPost">
                                        <?php echo htmlspecialchars($row['subject']); ?> from <b><?php echo htmlspecialchars($row['author']); ?></b></a><br>
                                        <small>
                                            <?php echo $row['date']; ?><br>
                                            &nbsp;Posted by <a href="/profile.php?id=<?php echo getIDFromUser($row['author'], $conn); ?>"><?php echo $row['author']; ?></a><br>
                                            <span id="floatRight"><a href="view.php?id=<?php echo $row['id']; ?>"><button>More Info</button></a></span><br>
                                            <?php $likes = (int)getLikesFromBlog($row['id'], $conn); ?>
                                            <?php $dislikes = (int)getDislikesFromBlog($row['id'], $conn); ?>
                                            <?php
                                                $total = $likes + $dislikes;
                                                if($total > 0)
                                                    $percent = round(($likes / $total) * 100);
                                                else 
                                                $percent = 100;
                                            ?>
                                            <div id="rating_score" class="rating" style="display: inline-block;">Rating:<strong><?php echo $percent; ?>%</strong></div>
                                            <div id="rate_btns" style="display: inline-block;">
                                                <div id="rate_yes"><a href="like.php?id=<?php echo $row['id']; ?>">Booyah !</a></div>
                                                <div id="rate_no"><a href="dislike.php?id=<?php echo $row['id']; ?>">No Way !</a></div>
                                            </div>
                                        </small>
                                    </span>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <?php require($_SERVER['DOCUMENT_ROOT'] . "/static/footer.php"); ?>
    </body>
</html>
