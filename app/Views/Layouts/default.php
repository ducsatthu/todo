<!DOCTYPE html>
    <html>
    <head>
        <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <title>TODO List</title>
        <link rel="stylesheet" href="<?php echo $data['base_url']; ?>/css/app.css">
        <style> [v-cloak] { display: none; } </style>
    </head>
    <body>
    <div id="app">
        <v-app>
        <?php echo $content ?>
        </v-app>
    </div>
    <script src="<?php echo $data['base_url']; ?>/scripts/app.js"></script>
    </body>
    </html>