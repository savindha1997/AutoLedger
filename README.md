# AutoLedger

## Deploy to cPanel

The GitHub Actions workflow deploys the repository to cPanel over FTPS whenever
you push to the `savi` branch. You can also start a deployment manually from the
repository's **Actions** tab.

In the repository's **Settings → Secrets and variables → Actions**, add these
repository secrets:

| Secret | Value |
| --- | --- |
| `CPANEL_FTP_SERVER` | Your hosting FTP hostname (for example, `ftp.example.com`) |
| `CPANEL_FTP_USERNAME` | Your cPanel FTP account username |
| `CPANEL_FTP_PASSWORD` | That FTP account's password |
| `CPANEL_FTP_SERVER_DIR` | The target directory, such as `public_html/` (include the trailing `/`) |

Create an FTP account in cPanel with access limited to the website directory,
and confirm that your hosting provider supports explicit FTPS on port 21.
The workflow intentionally excludes `dbconfig.php`, so it will not upload local
database settings or overwrite the production configuration. After the first
deployment, create `dbconfig.php` in the target directory using cPanel's File
Manager and configure it with the production database credentials from your
host. The workflow does not provision the database; keep production credentials
out of Git.
