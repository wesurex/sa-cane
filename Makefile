# Sobe os containers em segundo plano
up:
	docker compose up -d

# Para e remove os containers, volumes e rede
down:
	docker compose down 

# Mata todos os containers Docker em execução no sistema
killdb:
	sudo docker kill $$(sudo docker ps -q)