<section class="banner-contato">
        <div class="container">
            <h1>Entre em Contato</h1>
            <p>Estamos prontos para atendê-lo. Entre em contato conosco e tire suas dúvidas.</p>
        </div>
    </section>

    <section class="formulario-contato">
        <div class="container">
            <h2>Fale Conosco</h2>
            <form action="contato.php" method="POST">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="telefone">Telefone:</label>
                    <input type="text" id="telefone" name="telefone" required>
                </div>
                <div class="form-group">
                    <label for="mensagem">Mensagem:</label>
                    <textarea id="mensagem" name="mensagem" rows="6" required></textarea>
                </div>
                <button type="submit">Enviar Mensagem</button>
            </form>
        </div>
    </section>

    <section class="informacoes-contato">
        <div class="container">
            <div class="info-box">
                <h3>Endereço</h3>
                <p>Rua Exemplo, 123, Centro<br>Florianópolis - SC, 88000-000</p>
            </div>
            <div class="info-box">
                <h3>Email</h3>
                <p>contato@anageimoveis.com.br</p>
            </div>
            <div class="info-box">
                <h3>Telefone</h3>
                <p>(48) 1234-5678</p>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2024 ANAGE Imóveis. Todos os direitos reservados.</p>
        </div>
    </footer>

</body>
</html>