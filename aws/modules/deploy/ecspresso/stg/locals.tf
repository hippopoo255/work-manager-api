locals {
  repos = ["app", "web", "supervisor"]
  cluster_name = "${local.pj_name_camel}Cluster"
}