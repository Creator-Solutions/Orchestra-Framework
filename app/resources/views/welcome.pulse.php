<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Home Page</title>
  <link rel="stylesheet" href="/css/index.css">
</head>

<body>
  <div class="container">
    <header>
      <h1>Welcome to Orchestra-Framework</h1>
      <p>Your custom PHP framework is up and running!</p>
    </header>
    <main>
      <p>To get started, you can:</p>
      <ul>
        <li>Check out the <a href="https://github.com/creator-solutions/Orchestra-Framework">documentation</a></li>
        <li>Explore the <a href="https://github.com/creator-solutions/Orchestra-Framework">project's source code</a></li>
        <li>Modify the configuration files in <code>/app/config</code></li>
      </ul>
    </main>
    <footer>
      <p>&copy; <?php echo date('Y'); ?> Orchestra-Framework. All rights reserved.</p>
      <p> {{ message }}<p>
    </footer>
  </div>
</body>

</html>