<div class="result-content">
    <div class="filters-section position-relative">
        <div class="block-filters position-static">
            <form action="#" class="form">
                <div class="form-row">
                    <div class="col-md-3">
                        <div class="form-group normal-group black-group"><select class="form-control custom-select" id="searchForm-modality" name="statusLabel">
                                <option value="alugar">Alugar</option>
                                <option value="comprar">Comprar</option>
                                <option value="lancamentos">Lançamentos</option>
                            </select></div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group"><label for="searchForm-category" class="label-control">Tipo do Imóvel</label><select class="form-control custom-select" id="searchForm-category" name="category">
                                <option value="">Selecione o tipo</option>
                                <option value="22">Apartamento</option>
                                <option value="7">Casa/Sobrado</option>
                                <option value="11">Chácara/Sítio</option>
                                <option value="23">Comercial</option>
                                <option value="8">Terreno</option>
                            </select></div>
                    </div>
                    <div class="col-md-4">
                        <div class="block-control active">
                            <div class="block-toggle"><span class="text text-1">Selecione a cidade ou bairro</span>
                                <div><span class="text text-2">Joinville</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="block-control">
                            <div class="block-toggle"><span class="text text-1">Faixa de Preço (R$)</span></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="block-control">
                                    <div class="block-toggle"><span class="text text-1">Quartos</span></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="block-items">
                                    <p class="text text-1">Vagas Garagem</p>
                                    <div class="actions"><button type="button" class="btn btn-small active">1+</button><button type="button" class="btn btn-small">2+</button><button type="button" class="btn btn-small">3+</button><button type="button" class="btn btn-small">4+</button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-6"><button type="button" class="btn toggleSearch">Filtros Avançados</button></div>
                    <div class="col-md-6">
                        <div class="block-submit"><button type="submit" class="btn btn-1">Buscar meu Imóvel</button></div>
                    </div>
                </div>
            </form>
            <!-- <div class="switch-item "><button type="button" class="btn btn-switch ">Mapa</button></div> -->
        </div>
    </div>
</div>

<div class="properties-master-container">

    <h3>
        Imóveis para comprar em Joinville<br>
        <span>1072 Resultados</span>
    </h3>

    <div class="properties-container">
        <?php for ($i = 0; $i < 10; $i++): ?>
            <a href="?page=imovel" class="propertie">
                <div class="image" style="background-image: url(https://images.anageimoveis.com.br/vista.imobi/fotos/27959/i8955BLLt192xYuKXYC5H_2795966e3fb1f77281.jpg);"></div>
                <div class="infos-container">
                    <div class="type">Apartamento padrão</div>
                    <div class="neighborhood">Guanabara - Joinville</div>
                    <div class="price">R$ 250.000,00</div>
                    <div class="icons-info">
                        <ul>
                            <li>
                                <i class="fa-solid fa-bed"></i>
                                2 quartos
                            </li>
                            <li>
                                <i class="fa-regular fa-square"></i>
                                36m²
                            </li>
                            <li>
                                <i class="fa-solid fa-car"></i>
                                1 vaga
                            </li>
                        </ul>
                    </div>
                </div>
            </a>
        <?php endfor; ?>
    </div>

</div>