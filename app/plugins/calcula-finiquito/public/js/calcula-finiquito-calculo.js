const Calculo = {
	name: 'Calculo',
	template: `
	<!--	<pre>{{total}}</pre>-->
    <h1>Finiquito</h1>
    <div class="flex">
    	<div class="w-6-12 my-1">
    		Fecha ingreso
    		<br>
    		<b>{{total.inicio}}</b>
		</div>
    	<div class="w-6-12 my-1">
    		Fecha salida
    		<br>
    		<b>{{total.fin}}</b>
		</div>
	</div>
	
    <div class="flex">
    	<div class="w-6-12 my-1">
    		Tiempo trabajando
    		<br>
    		<b>{{total.dias}} días</b>
		</div>
    	<div class="w-6-12 my-1">
    		Vacaciones
    		<br>
    		<b>{{total.vacaciones_dias}} dias</b>
		</div>
	</div>
	
	<table>
		<tr><td>Salario mensual</td><td>$ {{num_format(total.salario)}}</td></tr>
		<tr><td>Salario devengado</td><td>$ {{num_format(total.denegado)}}</td></tr>
		<tr><td>Prima Antiguedad</td><td>$ {{num_format(total.prima_antiguedad)}}</td></tr>
		<tr><td>Vacaciones</td><td>$ {{num_format(total.vacaciones)}}</td></tr>
		<tr><td>Prima vacacional</td><td>$ {{num_format(total.prima_vacacional)}}</td></tr>
		<tr><td>Indemnización por despido</td><td>$ {{num_format(total.indemnizacion)}}</td></tr>
		<tr><td>TOTAL</td><td><h4>$ {{num_format(total.total)}}</h4></td></tr>
	</table>
	
	<h2>¿Cuánto cobrarías si presentas la demanda hoy?</h2>
	<table>
		<tr><td>Tope Salarios Caídos</td><td>$ {{num_format(total.salarios_caidos)}}</td></tr>
		<tr><td>Indemnización</td><td>$ {{num_format(total.total)}}</td></tr>
		<tr><td>TOTAL</td><td><h4>$ {{num_format(total.total_demanda)}}</h4></td></tr>
	</table> 
	
	<div class="tc">
		<button class="submit" @click="whats_send" class="w-6-12">¡Quiero demandar!</button>
	</div>

`,
	props:['input', 'whats'],
	computed:{
		total(){
			const {ingreso, salida} = this.input.fecha
			const {sueldo, nopagados} = this.input

			const MESES = [
				"Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio",
				"Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre",
			];

			const fecha_inicio = new Date(ingreso.ano, ingreso.mes-1, ingreso.dia)
			const inicio = `${ingreso.dia}/${MESES[ingreso.mes-1]}/${ingreso.ano}`
			const fecha_fin = new Date(salida.ano, salida.mes-1, salida.dia)
			const fin = `${salida.dia}/${MESES[salida.mes-1]}/${salida.ano}`

			const diffTime = Math.abs(fecha_inicio - fecha_fin)
			const dias = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

			const vacaciones_list = {
				1 : 12,
				2 : 14,
				3 : 16,
				4 : 18,
				5 : 20
			};

			[...Array(5)].forEach((_, i) => {
				vacaciones_list[i+6] = 22
				vacaciones_list[i+11] = 24
				vacaciones_list[i+16] = 26
				vacaciones_list[i+21] = 28
				vacaciones_list[i+26] = 30
				vacaciones_list[i+31] = 32
			})

			const vacaciones_dias = vacaciones_list[Math.floor(dias/365)]

			const salario = sueldo
			const denegado = salario * nopagados

			const sal_base = salario/30
			const ano_actual = new Date()
			const E3 = new Date(ano_actual.getFullYear(),0,1)
			const diasAno = Math.abs(fecha_fin - E3)
			const E4 = Math.ceil(diasAno / (1000 * 60 * 60 * 24))
			const E5 = E4/365
			const aguinaldo = sal_base*15*E5

			const vacaciones = sal_base*14*E5

			const prima_vacacional = 0.25*vacaciones

			const prima_antiguedad = 350*12

			const indemnizacion = sal_base*45

			const total = aguinaldo+vacaciones+prima_vacacional+indemnizacion+prima_antiguedad

			const salarios_caidos = sal_base * 30 * 6
			const total_demanda = salarios_caidos + total

			return {
				inicio,
				fin,
				dias,
				vacaciones_dias,
				salario,
				denegado,
				prima_antiguedad,
				vacaciones,
				prima_vacacional,
				indemnizacion,
				total,
				salarios_caidos,
				total_demanda
			}
		}
	},
	methods:{
		num_format(num){
			return 	parseFloat(num.toFixed(2)).toLocaleString({
				style: 'currency',
				currency: 'USD',
			})
		},
		whats_send(){
			const li = this.total
			const data = `Fecha ingreso: ${li.inicio}
					Fecha salida: ${li.fin}
					Tiempo trabajando: ${li.dias}
					Vacaciones: ${li.vacaciones_dias}
					Salario mensual: $ ${this.num_format(li.salario)}
					Salario devengado: $ ${this.num_format(li.denegado)}
					Prima Antiguedad: $ ${this.num_format(li.prima_antiguedad)}
					Vacaciones: $ ${this.num_format(li.vacaciones)}
					Prima vacacional: $ ${this.num_format(li.prima_vacacional)}
					Indemnización por despido: $ ${this.num_format(li.indemnizacion)}
					TOTAL: $ ${this.num_format(li.total)}
					Tope Salarios Caídos: $ ${this.num_format(li.salarios_caidos)}
					Total demanda: $ ${this.num_format(li.total_demanda)}
					
					. ${this.input.despidieron} ME DESPIDIERON!
					`

			const txt = `Quiero demandar! ${data}`

			window.open(`https://wa.me/${this.whats}?text=${encodeURI(txt)}`, '_blank');
			console.log('whats')
		}
	}
}