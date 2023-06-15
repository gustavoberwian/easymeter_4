<section role="main" class="content-body">

    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item me-2" role="energia">
            <button class="nav-link active pill-energy color-energy" id="pills-energia" data-bs-toggle="pill" data-bs-target="#energia" type="button" role="tab" aria-controls="energia" aria-selected="true">Energia</button>
        </li>
        <li class="nav-item" role="agua">
            <button class="nav-link pill-water color-agua" id="pills-agua" data-bs-toggle="pill" data-bs-target="#agua" type="button" role="tab" aria-controls="agua" aria-selected="false">Água</button>
        </li>
    </ul>

    <div class="tab-content configs" id="pills-tabContent">
        <div class="tab-pane fade show active" id="energia" role="tabpanel" aria-labelledby="pills-home-tab">
            <div class="row">
                <section class="card card-easymeter mb-4 col-6">
                    <div class="card-body documentation">
                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/energy/1.0/param?q=<span class="text-primary">list</span>[&t=<span class="text-warning">{type}</span>]&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Lista todos dispositivos disponíveis</td>
                            </tr>
                            <tr>
                                <td><code>t</code></td>
                                <td><span class="sub">opcional</span></td>
                                <td>Filtra a lista pelo tipo: 1: Área Comum 2: Unidade</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status": "success",
    "data": [
        {
            "device": "03D24E7A",
            "name": "Local 01",
            "type": 1 <span class="text-success">// 1: Área Comum 2: Unidade</span>
        },
        ...
        {
            "device": "03D244AC",
            "name": "Local 05",
            "type": 1
        }
    ]
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card card-easymeter mt-0 mb-4 col-6">
                    <div class="card-body documentation">
                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/energy/1.0/param?q=<span class="text-primary">resume</span>[&t=<span class="text-warning">{type}</span>]&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Obtém os dados de consumo dos medidores no mês atual</td>
                            </tr>
                            <tr>
                                <td><code>t</code></td>
                                <td><span class="sub">opcional</span></td>
                                <td>Filtra a lista pelo tipo: 1: Área Comum 2: Unidade</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status": "success",
    "data": [
        {
            "device": "03D24E7A",
            "name": "Local 01",
            "type": 1,                  <span class="text-success">// 1: Área Comum 2: Unidade</span>
            "current": "028941",        <span class="text-success">// Leitura atual do medidor - kWh</span>
            "month":5341.834,           <span class="text-success">// Consumo no mês - kWh</span>
            "month_opened":4147.885,    <span class="text-success">// Consumo no mês aberto - kWh</span>
            "month_closed":1193.949,    <span class="text-success">// Consumo no mês fechado - kWh</span>
            "ponta":1023.023,           <span class="text-success">// Consumo em horário de ponta - kWh</span>
            "fora":4318.811,            <span class="text-success">// Consumo em horário fora de ponta - kWh</span>
            "last":501.680,             <span class="text-success">// Consumo nas últimas 24 horas - kWh</span>
            "prevision":8798.315        <span class="text-success">// Previsão de consumo no mês - kWh</span>
        },
        ...
    ]
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                </section>


                <section class="card card-easymeter mt-0 mb-4 col-6">
                    <div class="card-body documentation">
                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/energy/1.0/param?q=<span class="text-primary">consumption</span>&d=<span class="text-warning">{device}</span>&s=<span class="text-warning">{data inicio}</span>&e=<span class="text-warning">{data fim}</span>&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Obtém o consumo do medidor no período, separado por posto tarifárico</td>
                            </tr>
                            <tr>
                                <td><code>d</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Código identificador do medidor</td>
                            </tr>
                            <tr>
                                <td><code>s</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data inicial do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td><code>e</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data final do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>
                            <tr>
                                <td colspan="3">Se a data inicial e final forem iguais, os resultados serão retornados por hora.</td>
                            </tr>

                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status": "success",
    "data": [
        {
            "name": "Fora Ponta",           <span class="text-success">// Posto tarifárico</span>
            "data": [
                ["2022-02-01", 385.528],     <span class="text-success">// Data/Hora e Consumo no dia/hora - kWh</span>
                ["2022-02-02", 352.965]
            ]
        },
        {
            "name": "Ponta",
            "data": [
                ["2022-02-01", 115.692],
                ["2022-02-02", 121.365]
            ]
        }
    ]
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>


                <section class="card card-easymeter mt-0 mb-4 col-6">
                    <div class="card-body documentation">
                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/energy/1.0/param?q=<span class="text-primary">active_demand</span>&d=<span class="text-warning">{device}</span>&s=<span class="text-warning">{data inicio}</span>&e=<span class="text-warning">{data fim}</span>&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Obtém a demanda de energia ativa do medidor no período</td>
                            </tr>
                            <tr>
                                <td><code>d</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Código identificador do medidor</td>
                            </tr>
                            <tr>
                                <td><code>s</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data inicial do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td><code>e</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data final do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>
                            <tr>
                                <td colspan="3">Se a data inicial e final forem iguais, os resultados serão retornados por hora.</td>
                            </tr>

                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status": "success",
    "data": [
        {
            "name": "Demanda Máxima",
            "data": [
                ["2022-02-01",38.133],      <span class="text-success">// Data/Hora e Demanda Máxima no dia/hora - kW</span>
                ["2022-02-02",37.687]
            ]
        },
        {
            "name":"Demanda Média",         <span class="text-success">// Data/Hora e Demanda Média no dia/hora - kW</span>
            "data":[
                ["2022-02-01",20.862],
                ["2022-02-02",19.739]
            ]
        }
    ]
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card card-easymeter mt-0 mb-4 col-6">
                    <div class="card-body documentation">

                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/energy/1.0/param?q=<span class="text-primary">reactive</span>&d=<span class="text-warning">{device}</span>&s=<span class="text-warning">{data inicio}</span>&e=<span class="text-warning">{data fim}</span>&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Obtém a energia reativa gerada pelo medidor no período</td>
                            </tr>
                            <tr>
                                <td><code>d</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Código identificador do medidor</td>
                            </tr>
                            <tr>
                                <td><code>s</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data inicial do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td><code>e</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data final do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>
                            <tr>
                                <td colspan="3">Se a data inicial e final forem iguais, os resultados serão retornados por hora.</td>
                            </tr>

                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status": "success",
    "data": [
        {
            "name": "Reativa Capacitiva",
            "data": [
                ["2022-02-01",17.799],  <span class="text-success">// Data/Hora e Reativa Capacitiva no dia/hora - kVArh</span>
                ["2022-02-02",18.202]
            ]
        },
        {
            "name":"Reativa Indutiva",  <span class="text-success">// Data/Hora e Reativa Indutiva no dia/hora - kVArh</span>
            "data":[
                ["2022-02-01",180.722],
                ["2022-02-02",161.989]
            ]
        }
    ]
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card card-easymeter mt-0 mb-4 col-6">
                    <div class="card-body documentation">

                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/energy/1.0/param?q=<span class="text-primary">load</span>&d=<span class="text-warning">{device}</span>&s=<span class="text-warning">{data inicio}</span>&e=<span class="text-warning">{data fim}</span>&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Obtém o fator de carga do medidor no período</td>
                            </tr>
                            <tr>
                                <td><code>d</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Código identificador do medidor</td>
                            </tr>
                            <tr>
                                <td><code>s</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data inicial do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td><code>e</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data final do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>
                            <tr>
                                <td colspan="3">Se a data inicial e final forem iguais, os resultados serão retornados por hora.</td>
                            </tr>

                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status": "success",
    "data": [
        {
            "name": "Fator de Carga",
            "data": [
                ["2022-02-01",0.547],  <span class="text-success">// Data/Hora e Fator de Carga no dia/hora</span>
                ["2022-02-02",0.520]
            ]
        }
    ]
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card card-easymeter mt-0 mb-4 col-6">
                    <div class="card-body documentation">

                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/energy/1.0/param?q=<span class="text-primary">instant_active</span>&d=<span class="text-warning">{device}</span>&s=<span class="text-warning">{data inicio}</span>&e=<span class="text-warning">{data fim}</span>&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Obtém a potência ativa instantânea por fase do medidor no período</td>
                            </tr>
                            <tr>
                                <td><code>d</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Código identificador do medidor</td>
                            </tr>
                            <tr>
                                <td><code>s</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data inicial do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td><code>e</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data final do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>
                            <tr>
                                <td colspan="3">Se a data inicial e final forem iguais, os resultados serão retornados por hora.</td>
                            </tr>

                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status": "success",
    "data": [
        {
            "name": "Fase R",
            "data": [
                ["2022-02-01",1242.342],  <span class="text-success">// Data/Hora e Fase R no dia/hora - kW</span>
                ["2022-02-02",1196.354]
            ]
        },
        {
            "name": "Fase S",
            "data": [
                ["2022-02-01",807.835],  <span class="text-success">// Data/Hora e Fase S no dia/hora - kW</span>
                ["2022-02-02",742.053]
            ]
        },
        {
            "name": "Fase T",
            "data": [
                ["2022-02-01",947.628],  <span class="text-success">// Data/Hora e Fase T no dia/hora - kW</span>
                ["2022-02-02",904.828]
            ]
        }
    ]
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card card-easymeter mt-0 mb-4 col-6">
                    <div class="card-body documentation">

                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/energy/1.0/param?q=<span class="text-primary">instant_current</span>&d=<span class="text-warning">{device}</span>&s=<span class="text-warning">{data inicio}</span>&e=<span class="text-warning">{data fim}</span>&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Obtém a corrente média por fase do medidor no período</td>
                            </tr>
                            <tr>
                                <td><code>d</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Código identificador do medidor</td>
                            </tr>
                            <tr>
                                <td><code>s</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data inicial do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td><code>e</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data final do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>
                            <tr>
                                <td colspan="3">Se a data inicial e final forem iguais, os resultados serão retornados por hora.</td>
                            </tr>

                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status": "success",
    "data": [
        {
            "name": "Fase R",
            "data": [
                ["2022-02-01",41.323],  <span class="text-success">// Data/Hora e Fase R no dia/hora - A</span>
                ["2022-02-02",39.556]
            ]
        },
        {
            "name": "Fase S",
            "data": [
                ["2022-02-01",28.792],  <span class="text-success">// Data/Hora e Fase S no dia/hora - A</span>
                ["2022-02-02",26.388]
            ]
        },
        {
            "name": "Fase T",
            "data": [
                ["2022-02-01",32.973],  <span class="text-success">// Data/Hora e Fase T no dia/hora - A</span>
                ["2022-02-02",31.419]
            ]
        }
    ]
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card card-easymeter mt-0 mb-4 col-6">
                    <div class="card-body documentation">

                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/energy/1.0/param?q=<span class="text-primary">instant_voltage</span>&d=<span class="text-warning">{device}</span>&s=<span class="text-warning">{data inicio}</span>&e=<span class="text-warning">{data fim}</span>&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Obtém a tensão média por fase do medidor no período</td>
                            </tr>
                            <tr>
                                <td><code>d</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Código identificador do medidor</td>
                            </tr>
                            <tr>
                                <td><code>s</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data inicial do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td><code>e</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data final do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>
                            <tr>
                                <td colspan="3">Se a data inicial e final forem iguais, os resultados serão retornados por hora.</td>
                            </tr>

                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status": "success",
    "data": [
        {
            "name": "Fase R",
            "data": [
                ["2022-02-01",219.898],  <span class="text-success">// Data/Hora e Fase R no dia/hora - V</span>
                ["2022-02-02",220.707]
            ]
        },
        {
            "name": "Fase S",
            "data": [
                ["2022-02-01",221.417],  <span class="text-success">// Data/Hora e Fase S no dia/hora - V</span>
                ["2022-02-02",222.142]
            ]
        },
        {
            "name": "Fase T",
            "data": [
                ["2022-02-01",218.945],  <span class="text-success">// Data/Hora e Fase T no dia/hora - V</span>
                ["2022-02-02",219.528]
            ]
        }
    ]
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card card-easymeter mt-0 mb-4 col-6">
                    <div class="card-body documentation">

                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/energy/1.0/param?q=<span class="text-primary">instant_power</span>&d=<span class="text-warning">{device}</span>&s=<span class="text-warning">{data inicio}</span>&e=<span class="text-warning">{data fim}</span>&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Obtém a potência máxima instantânea por fase do medidor no período</td>
                            </tr>
                            <tr>
                                <td><code>d</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Código identificador do medidor</td>
                            </tr>
                            <tr>
                                <td><code>s</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data inicial do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td><code>e</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data final do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>
                            <tr>
                                <td colspan="3">Se a data inicial e final forem iguais, os resultados serão retornados por hora.</td>
                            </tr>

                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status": "success",
    "data": [
        {
            "name": "Fase R",
            "data": [
                ["2022-02-01",14.642],  <span class="text-success">// Data/Hora e Fase R no dia/hora - kW</span>
                ["2022-02-02",13.908]
            ]
        },
        {
            "name": "Fase S",
            "data": [
                ["2022-02-01",12.228],  <span class="text-success">// Data/Hora e Fase S no dia/hora - kW</span>
                ["2022-02-02",11.537]
            ]
        },
        {
            "name": "Fase T",
            "data": [
                ["2022-02-01",13.271],  <span class="text-success">// Data/Hora e Fase T no dia/hora - kW</span>
                ["2022-02-02",13.157]
            ]
        }
    ]
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card card-easymeter mt-0 mb-4 col-6">
                    <div class="card-body documentation">

                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/energy/1.0/param?q=<span class="text-primary">instant_load</span>&d=<span class="text-warning">{device}</span>&s=<span class="text-warning">{data inicio}</span>&e=<span class="text-warning">{data fim}</span>&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Obtém o fator de carga instantâneo por fase do medidor no período</td>
                            </tr>
                            <tr>
                                <td><code>d</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Código identificador do medidor</td>
                            </tr>
                            <tr>
                                <td><code>s</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data inicial do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td><code>e</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data final do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>
                            <tr>
                                <td colspan="3">Se a data inicial e final forem iguais, os resultados serão retornados por hora.</td>
                            </tr>

                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status": "success",
    "data": [
        {
            "name": "Fase R",
            "data": [
                ["2022-02-01",0.589],  <span class="text-success">// Data/Hora e Fase R no dia/hora</span>
                ["2022-02-02",0.597]
            ]
        },
        {
            "name": "Fase S",
            "data": [
                ["2022-02-01",0.459],  <span class="text-success">// Data/Hora e Fase S no dia/hora</span>
                ["2022-02-02",0.447]
            ]
        },
        {
            "name": "Fase T",
            "data": [
                ["2022-02-01",0.496],  <span class="text-success">// Data/Hora e Fase T no dia/hora</span>
                ["2022-02-02",0.478]
            ]
        }
    ]
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card card-easymeter mt-0 mb-4 col-6">
                    <div class="card-body documentation">

                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/energy/1.0/param?q=<span class="text-primary">instant_reactive</span>&d=<span class="text-warning">{device}</span>&s=<span class="text-warning">{data inicio}</span>&e=<span class="text-warning">{data fim}</span>&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <h4>Obtém a demanda de energia ativa do medidor no período</h4>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Obtém a energia reativa instantânea por fase do medidor no período</td>
                            </tr>
                            <tr>
                                <td><code>d</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Código identificador do medidor</td>
                            </tr>
                            <tr>
                                <td><code>s</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data inicial do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td><code>e</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data final do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>
                            <tr>
                                <td colspan="3">Se a data inicial e final forem iguais, os resultados serão retornados por hora.</td>
                            </tr>

                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status": "success",
    "data": [
        {
            "name": "Fase R",
            "data": [
                ["2022-02-01",1242.342],  <span class="text-success">// Data/Hora e Fase R no dia/hora - kVAr</span>
                ["2022-02-02",1196.354]
            ]
        },
        {
            "name": "Fase S",
            "data": [
                ["2022-02-01",807.835],  <span class="text-success">// Data/Hora e Fase S no dia/hora - kVAr</span>
                ["2022-02-02",742.053]
            ]
        },
        {
            "name": "Fase T",
            "data": [
                ["2022-02-01",947.628],  <span class="text-success">// Data/Hora e Fase T no dia/hora - kVAr</span>
                ["2022-02-02",904.828]
            ]
        }
    ]
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card card-easymeter mt-0 mb-4 col-6">
                    <div class="card-body documentation">

                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/energy/1.0/param?q=<span class="text-primary">instant_factor</span>&d=<span class="text-warning">{device}</span>&s=<span class="text-warning">{data inicio}</span>&e=<span class="text-warning">{data fim}</span>&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Obtém o fator de potência instantâneo por fase do medidor no período</td>
                            </tr>
                            <tr>
                                <td><code>d</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Código identificador do medidor</td>
                            </tr>
                            <tr>
                                <td><code>s</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data inicial do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td><code>e</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data final do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>
                            <tr>
                                <td colspan="3">Se a data inicial e final forem iguais, os resultados serão retornados por hora.</td>
                            </tr>

                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status": "success",
    "data": [
        {
            "name": "Fase R",
            "data": [
                ["2022-02-01",-0.952],  <span class="text-success">// Data/Hora e Fase R no dia/hora</span>
                ["2022-02-02",-0.957]
            ]
        },
        {
            "name": "Fase S",
            "data": [
                ["2022-02-01",0.885],  <span class="text-success">// Data/Hora e Fase S no dia/hora</span>
                ["2022-02-02",0.888]
            ]
        },
        {
            "name": "Fase T",
            "data": [
                ["2022-02-01",0.933],  <span class="text-success">// Data/Hora e Fase T no dia/hora</span>
                ["2022-02-02",0.938]
            ]
        }
    ]
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card card-easymeter mt-0 mb-4 col-6">
                    <div class="card-body documentation">
                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/energy/1.0/param?q=<span class="text-primary">accountings</span>&p=<span class="text-warning">{pag}</span>&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Obtém a lista de lançamentos em ordem cronológica decrescente, paginados de 10 em 10 registros</td>
                            </tr>
                            <tr>
                                <td><code>p</code></td>
                                <td><span class="sub">opcional</span></td>
                                <td>Página a ser obtida. Se não informado, retorna os últimos 10 registros</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status":"success",
    "data":[
        {
            "id":9,
            "competence":"02/2022",             <span class="text-success">// Mês de competência</span>
            "start":"2023-01-01",
            "end":"2023-01-16",
            "common_consumption":28521.449,     <span class="text-success">// Consumo da Área Comum - kWh</span>
            "common_ponta":4308.885,            <span class="text-success">// Consumo da Área Comum em horário de ponta - kWh</span>
            "common_fora":24215.075,            <span class="text-success">// Consumo da Área Comum em horário fora de ponta - kWh</span>
            "common_demand":61.157,             <span class="text-success">// Demanda máxima da Área Comum - kW</span>
            "units_consumption":13151.572,      <span class="text-success">// Consumo das Unidades - kWh</span>
            "units_ponta":"2324.000",           <span class="text-success">// Consumo das Unidades em horário de ponta - kWh</span>
            "units_fora":10833.778,             <span class="text-success">// Consumo das Unidades em horário fora de ponta - kWh</span>
            "units_demand":38.593,              <span class="text-success">// Demanda máxima das Unidades - kW</span>
            "date":"2023-01-25"
        },
        ...
    ]
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card card-easymeter mt-0 mb-4 col-6">
                    <div class="card-body documentation">
                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/energy/1.0/param?q=<span class="text-primary">accounting</span>&i=<span class="text-warning">{id}</span>&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Obtém os dados de consumo dos medidores no lançamento</td>
                            </tr>
                            <tr>
                                <td><code>i</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Id do lançamento</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status":"success",
    "data":[
        {
            "device":"03D24E7A",
            "name":"Local 01",
            "previous_read":"000137",       <span class="text-success">// Leitura anterior</span>
            "current_read":"000539",        <span class="text-success">// Leitura atual</span>
            "consumption":402.516,          <span class="text-success">// Consumo no período - kWh</span>
            "consumption_ponta":78.263,     <span class="text-success">// Consumo em horário de ponta no período - kWh</span>
            "consumption_fora":324.253,     <span class="text-success">// Consumo em horário fora de ponta no período - kWh</span>
            "demand":3.819,                 <span class="text-success">// Demanda máxima no período - kW</span>
            "demand_ponta":3.595,           <span class="text-success">// Demanda máxima em horário de ponta no período - kW</span>
            "demand_fora":3.819             <span class="text-success">// Demanda máxima em horário fora de ponta no período - kW</span>
        },
        ...
    ],
    "message":""
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card card-easymeter mt-0 mb-4 col-6">
                    <div class="card-body documentation">
                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/energy/1.0/param?q=<span class="text-primary">add_accounting</span>&c=<span class="text-warning">{competência}</span>&s=<span class="text-warning">{data início}</span>&c=<span class="text-warning">{data fim}</span>&m=<span class="text-warning">{mensagem}</span>&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Cria um novo lançamento</td>
                            </tr>
                            <tr>
                                <td><code>c</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Competência do lançamento no formato DD-YYYY</td>
                            </tr>
                            <tr>
                                <td><code>s</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data inicial do lançamento no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td><code>e</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data final do lançamento no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td><code>m</code></td>
                                <td><span class="sub">opcional</span></td>
                                <td>Mensagem a ser inserida no lançamento</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>

                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status":"success",
    "accounting":24,        <span class="text-success">// Id do lançamento inserido</span>
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

            </div>
        </div>

        <div class="tab-pane fade" id="agua" role="tabpanel" aria-labelledby="pills-profile-tab">
            <div class="row">
                <section class="card card-easymeter mb-4 col-6">
                    <div class="card-body documentation">
                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/water/1.0/param?q=<span class="text-primary">list</span>[&t=<span class="text-warning">{type}</span>]&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Lista todos dispositivos disponíveis</td>
                            </tr>
                            <tr>
                                <td><code>t</code></td>
                                <td><span class="sub">opcional</span></td>
                                <td>Filtra a lista pelo tipo: 1: Área Comum 2: Unidade</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status": "success",
    "data": [
        {
            "device": "03D24E7A",
            "name": "Local 01",
            "type": 1 <span class="text-success">// 1: Área Comum 2: Unidade</span>
        },
        ...
        {
            "{device}": "03D244AC",
            "name": "Local 05",
            "type": 1
        }
    ]
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card card-easymeter mt-0 mb-4 col-6">
                    <div class="card-body documentation">
                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/water/1.0/param?q=<span class="text-primary">resume</span>[&t=<span class="text-warning">{type}</span>]&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Obtém os dados de consumo dos medidores no mês atual</td>
                            </tr>
                            <tr>
                                <td><code>t</code></td>
                                <td><span class="sub">opcional</span></td>
                                <td>Filtra a lista pelo tipo: 1: Área Comum 2: Unidade</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status": "success",
    "data": [
        {
            "device": "03D24E7A",
            "name": "Local 01",
            "type": 1,                  <span class="text-success">// 1: Área Comum 2: Unidade</span>
            "current": "028941",        <span class="text-success">// Leitura atual do medidor - L</span>
            "month":5341.834,           <span class="text-success">// Consumo no mês - L</span>
            "month_opened":4147.885,    <span class="text-success">// Consumo no mês aberto - L</span>
            "month_closed":1193.949,    <span class="text-success">// Consumo no mês fechado - L</span>
            "ponta":1023.023,           <span class="text-success">// Consumo em horário de ponta - L</span>
            "fora":4318.811,            <span class="text-success">// Consumo em horário fora de ponta - L</span>
            "last":501.680,             <span class="text-success">// Consumo nas últimas 24 horas - L</span>
            "prevision":8798.315        <span class="text-success">// Previsão de consumo no mês - L</span>
        },
        ...
    ]
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                </section>

                <section class="card card-easymeter mt-0 mb-4 col-6">
                    <div class="card-body documentation">
                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/water/1.0/param?q=<span class="text-primary">consumption</span>&d=<span class="text-warning">{device}</span>&s=<span class="text-warning">{data inicio}</span>&e=<span class="text-warning">{data fim}</span>&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Obtém o consumo de água do medidor</td>
                            </tr>
                            <tr>
                                <td><code>d</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Código identificador do medidor</td>
                            </tr>
                            <tr>
                                <td><code>s</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data inicial do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td><code>e</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data final do período desejado no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>
                            <tr>
                                <td colspan="3">Se a data inicial e final forem iguais, os resultados serão retornados por hora.</td>
                            </tr>

                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status": "success",
    "name": "Consumo",
    "data": [
        ["2022-02-01",700],     <span class="text-success">// Data/Hora e Consumo no dia/hora - L</span>
        ["2022-02-02",1],
    ]
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card card-easymeter mt-0 mb-4 col-6">
                    <div class="card-body documentation">
                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/water/1.0/param?q=<span class="text-primary">accountings</span>&p=<span class="text-warning">{pag}</span>&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Obtém a lista de lançamentos em order cronológica decrescente, paginados de 10 em 10 registros</td>
                            </tr>
                            <tr>
                                <td><code>p</code></td>
                                <td><span class="sub">opcional</span></td>
                                <td>Página a ser obtida. Se não informado, retorna os últimos 10 registros</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status": "success",
    "data": [
        {
            "id":23,
            "competence":12/2022,           <span class="text-success">// Mês de competência</span>
            "start":"2022-01-31",
            "end":"2022-02-15",
            "consumption":30810,            <span class="text-success">// Consumo no mês - L</span>
            "consumption_opened":20042,     <span class="text-success">// Consumo no mês aberto - L</span>
            "consumption_closed":10868,     <span class="text-success">// Consumo no mês fechado - L</span>
            "date":"2022-02-17",            <span class="text-success">// Data de emissão</span>
        },
        ...
    ]
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card card-easymeter mt-0 mb-4 col-6">
                    <div class="card-body documentation">
                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/water/1.0/param?q=<span class="text-primary">accounting</span>&i=<span class="text-warning">{id}</span>&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Obtém os dados de consumo dos medidores no lançamento</td>
                            </tr>
                            <tr>
                                <td><code>i</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Id do lançamento</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status":"success",
    "data": [
        {
            "device":"03D24E7A",
            "name":"Local 01",
            "previous_read":"012266",       <span class="text-success">// Leitura anterior</span>
            "current_read":"018783",        <span class="text-success">// Leitura atual</span>
            "consumption":6517,             <span class="text-success">// Consumo no mês - L</span>
            "consumption_opened":3477,      <span class="text-success">// Consumo no mês aberto - L</span>
            "consumption_closed":3040,      <span class="text-success">// Consumo no mês fechado - L</span>
        },
        ...
    ],
    "message":""
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="card card-easymeter mt-0 mb-4 col-6">
                    <div class="card-body documentation">
                        <blockquote class="mt-3">
                            <p class="m-0 text-3"><b>http://www.easymeter.io/api/water/1.0/param?q=<span class="text-primary">add_accounting</span>&c=<span class="text-warning">{competência}</span>&s=<span class="text-warning">{data início}</span>&c=<span class="text-warning">{data fim}</span>&m=<span class="text-warning">{mensagem}</span>&key=<span class="text-warning">{chave}</span></b></p>
                        </blockquote>
                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Parâmetros</th>
                            </tr>
                            <tr>
                                <td><code>q</code></td>
                                <td><span class="sub">fixo</span></td>
                                <td>Cria um novo lançamento</td>
                            </tr>
                            <tr>
                                <td><code>c</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Competência do lançamento no formato DD-YYYY</td>
                            </tr>
                            <tr>
                                <td><code>s</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data inicial do lançamento no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td><code>e</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Data final do lançamento no formato YYYY-MM-DD</td>
                            </tr>
                            <tr>
                                <td><code>m</code></td>
                                <td><span class="sub">opcional</span></td>
                                <td>Mensagem a ser inserida no lançamento</td>
                            </tr>
                            <tr>
                                <td width="8%"><code>key</code></td>
                                <td width="16%"><span class="sub">obrigatório</span></td>
                                <td>Sua chave única (ela encontra-se no seu painel, no item configurações)</td>
                            </tr>

                            </tbody>
                        </table>

                        <table class="api-table">
                            <tbody>
                            <tr>
                                <th colspan="3">Resposta</th>
                            </tr>
                            <tr>
                                <td>
                                        <pre class="mb-0"><code class="answer">{
    "status":"success",
    "accounting":24,        <span class="text-success">// Id do lançamento inserido</span>
}</code></pre>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

            </div>
        </div>
    </div>
</section>
