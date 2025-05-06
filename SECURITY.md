# Mesures de protection contre le vol de code

Ce document décrit les mesures de sécurité mises en place pour protéger ce projet contre le vol et l'utilisation non autorisée.

## Protections implémentées

1. **Protection légale**
   - Licence propriétaire interdisant toute réutilisation sans autorisation
   - Copyright explicite dans le fichier LICENSE

2. **Protection des fichiers source**
   - Fichiers `.htaccess` pour empêcher l'accès direct aux répertoires sensibles
   - Blocage de l'accès aux fichiers de configuration et scripts internes

3. **Vérification d'intégrité**
   - Système de checksums pour détecter les modifications non autorisées
   - Blocage de l'application en cas de détection de fichiers modifiés

4. **Système de licence**
   - Vérification de la validité de la licence
   - Restriction de l'utilisation à des domaines spécifiques
   - Dates d'expiration pour les licences

5. **Obfuscation**
   - Possibilité d'encoder certaines parties sensibles du code
   - Protection contre l'analyse statique

6. **Sécurité active**
   - Détection des tentatives d'intrusion (injections SQL, XSS, etc.)
   - Journalisation des tentatives d'attaque

## Utilisation des outils de sécurité

### Génération d'une licence de développement

```bash
bin/create-license [proprietaire] [domaine] [date_expiration]
```

Exemple:
```bash
bin/create-license "Sidy" "monsite.com" "2026-01-01"
```

Sans arguments, une licence pour localhost valable 1 an sera générée.

### Génération des checksums

```bash
bin/generate-checksums
```

Cette commande crée un fichier `config/checksums.php` contenant les empreintes MD5 des fichiers critiques. Les modifications non autorisées de ces fichiers seront détectées.

## Personnalisation des protections

Pour adapter les protections à vos besoins :

1. Modifier le fichier `src/Security/LicenseChecker.php` pour ajuster la vérification de licence
2. Modifier le fichier `bin/generate-checksums` pour ajouter/supprimer des fichiers à surveiller
3. Adapter le fichier `public/index.php` pour ajuster les domaines autorisés

## Déploiement sécurisé

Pour un déploiement sécurisé :

1. Exécutez `bin/generate-checksums` après chaque mise à jour du code
2. Générez une licence valide avec `bin/create-license`
3. Assurez-vous que les fichiers `.htaccess` sont correctement déployés
4. Utilisez HTTPS pour toutes les communications

**Note importante :** Ces mesures de protection ne sont pas infaillibles, mais créent plusieurs couches de sécurité rendant plus difficile le vol et l'utilisation non autorisée du code. 