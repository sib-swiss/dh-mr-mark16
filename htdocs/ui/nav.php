
            <nav class="navbar navbar-expand-sm navbar-light" role="navigation">
                <a class="navbar-brand" href="<?php echo $f3->get('MR_PATH_WEB'); ?>content-old">
                    <img src="resources/frontend/img/logo-manuscript-blue.png" style="width: 200px">
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon">
                </button>
                <div class="collapse navbar-collapse col-10" id="navbarSupportedContent">
                    <div class="navbar-nav mr-auto">

                        <?php
                        // Navigation pages
                        $pages = [
                            ['href' => 'about-old', 'name' => 'About'],
                            ['href' => 'content-old', 'name' => 'Content'],
                            ['href' => 'search-old', 'name' => 'Advanced Search']
                        ];
                        $nav_links = '';

                        // Generate navigation links
                        foreach ($pages as $page) {
                            // Create navigation link
                            $nav_links .= '<a class="nav-item nav-link';

                            // Define active class
                            if ($page['name'] === 'Content') {
                                $nav_links .= ($f3->get('PATH') === '/' . $page['href'] || $f3->get('PATH') === '/show' ? ' active"' : '"');
                            }
                            else {
                                $nav_links .= ($f3->get('PATH') === '/' . $page['href'] ? ' active"' : '"');
                            }

                            // Define link href
                            $nav_links .= 'href="' . $f3->get('MR_PATH_WEB') . $page['href'] . '"';
                            $nav_links .= '>';

                            // Define link name
                            $nav_links .= $page['name'];

                            // Define screen reader tag
                            if ($page['name'] === 'Content') {
                                $nav_links .= ($f3->get('PATH') === '/' . $page['href'] || $f3->get('PATH') === '/show' ? '<span class="sr-only">(current)</span>' : '');
                            }
                            else {
                                $nav_links .= ($f3->get('PATH') === '/' . $page['href'] ? '<span class="sr-only">(current)</span>' : '');
                            }

                            // Close navigation link
                            $nav_links .= '</a>' . PHP_EOL;
                        }

                        // Display generated navigation links
                        echo $nav_links;
                        ?>

                    </div>
                    <form action="<?php echo $f3->get('MR_PATH_WEB') . 'results'; ?>" method="get" class="form-inline my-2 my-lg-0">
                        <div class="input-group">
                            <input type="text" name="subject" class="form-control" placeholder="INTF Liste manuscript name" aria-label="Search" aria-describedby="basic-addon1" value="<?php echo (isset($_GET['subject']) ? htmlentities(strip_tags($_GET['subject'])) : ''); ?>" autofocus>
                            <button class="btn btn-search" type="submit" id="basic-addon1" onclick="if (document.getElementsByName('subject')[0].value === '') { return false; }">
                                Search
                            </button>
                            <!-- <div class="input-group-append">
                                <button class="btn btn-search" type="submit" id="basic-addon1">
                                    <i class="fas fa-search">
                                </button>
                            </div> -->
                        </div>
                    </form>
                </div>
            </nav>
