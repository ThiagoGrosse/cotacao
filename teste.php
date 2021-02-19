<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <p>Produtos</p>
    <form method="post" action="http://localhost/cotacao-2021/cotacaoMassiva" enctype="multipart/form-data">
        <p>
            <label>Add file (single): </label><br />
            <input type="file" name="example1" />
        </p>
        <button>Enviar</button>
    </form>

    <p>Pedidos</p>
    <form method="post" action="http://localhost/cotacao-2021/cotacaoMassivaPedidos" enctype="multipart/form-data">
        <p>
            <label>Add file (single): </label><br />
            <input type="file" name="formPedidos" />
        </p>
        <button>Enviar</button>
    </form>
</body>

</html>