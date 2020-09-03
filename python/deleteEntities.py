from neo4j import GraphDatabase

driver = GraphDatabase.driver("bolt://neo4j:7687", auth=("neo4j", "test"), encrypted=False)

session = driver.session()

node_id = '10'
query = "MATCH (rn:RootNode)--(cf:ContentField)--(sen:Sentence)--(tag:Tag) WHERE (rn.name = '" + node_id + "') OPTIONAL MATCH (sen)--(clause:Clause) DETACH DELETE clause, tag, sen, cf, rn "
result = session.run(query)
print(result)