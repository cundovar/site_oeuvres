"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import styles from "./connexion.module.scss";

export default function Connexion() {
  const navigateur = useRouter();
  const [mode, setMode] = useState<"lien" | "motDePasse">("lien");
  const [email, setEmail] = useState("");
  const [motDePasse, setMotDePasse] = useState("");
  const [enCours, setEnCours] = useState(false);
  const [message, setMessage] = useState<{ texte: string; erreur: boolean } | null>(null);

  async function demanderLien(evenement: React.FormEvent<HTMLFormElement>) {
    evenement.preventDefault();
    setEnCours(true);
    setMessage(null);
    try {
      const reponse = await fetch("/api/admin/connexion/lien/demande", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email }),
      });
      if (reponse.status === 202) {
        setMessage({
          texte: "Si cette adresse est autorisée, un lien de connexion vous a été envoyé. Il est valable 15 minutes.",
          erreur: false,
        });
        setEmail("");
      } else {
        setMessage({
          texte: reponse.status === 429 ? "Trop de demandes. Réessayez plus tard." : "La demande n’a pas pu être traitée.",
          erreur: true,
        });
      }
    } catch {
      setMessage({ texte: "Service indisponible. Réessayez plus tard.", erreur: true });
    } finally {
      setEnCours(false);
    }
  }

  async function seConnecter(evenement: React.FormEvent<HTMLFormElement>) {
    evenement.preventDefault();
    setEnCours(true);
    setMessage(null);
    try {
      const csrf = await fetch("/api/admin/csrf", { cache: "no-store" });
      const { csrf: jeton } = (await csrf.json()) as { csrf: string };
      const reponse = await fetch("/api/admin/connexion/mot-de-passe", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, motDePasse, csrf: jeton }),
      });
      if (reponse.ok) {
        navigateur.replace("/administration");
        return;
      }
      setMessage({
        texte:
          reponse.status === 429
            ? "Trop de tentatives. Réessayez dans un quart d’heure."
            : "Connexion refusée. Vérifiez l’adresse et le mot de passe.",
        erreur: true,
      });
    } catch {
      setMessage({ texte: "Service indisponible. Réessayez plus tard.", erreur: true });
    } finally {
      setEnCours(false);
    }
  }

  return (
    <main className={styles.main}>
      <header className={styles.entete}>
        <p className={styles.atelier}>L’ATELIER</p>
        <h1>Espace de gestion du site</h1>
      </header>

      {mode === "lien" ? (
        <form className={styles.formulaire} onSubmit={demanderLien}>
          <label htmlFor="email">Adresse e-mail</label>
          <input
            id="email"
            name="email"
            type="email"
            autoComplete="email"
            required
            value={email}
            onChange={(e) => setEmail(e.target.value)}
          />
          <button type="submit" disabled={enCours}>
            {enCours ? "Envoi en cours…" : "Recevoir le lien"}
          </button>
          <button
            type="button"
            className={styles.basculer}
            onClick={() => {
              setMode("motDePasse");
              setMessage(null);
            }}
          >
            Utiliser un mot de passe
          </button>
        </form>
      ) : (
        <form className={styles.formulaire} onSubmit={seConnecter}>
          <label htmlFor="email-mdp">Adresse e-mail</label>
          <input
            id="email-mdp"
            name="email"
            type="email"
            autoComplete="email"
            required
            value={email}
            onChange={(e) => setEmail(e.target.value)}
          />
          <label htmlFor="motDePasse">Mot de passe</label>
          <input
            id="motDePasse"
            name="motDePasse"
            type="password"
            autoComplete="current-password"
            required
            value={motDePasse}
            onChange={(e) => setMotDePasse(e.target.value)}
          />
          <button type="submit" disabled={enCours}>
            {enCours ? "Connexion…" : "Se connecter"}
          </button>
          <button
            type="button"
            className={styles.basculer}
            onClick={() => {
              setMode("lien");
              setMessage(null);
            }}
          >
            Recevoir un lien par e-mail
          </button>
        </form>
      )}

      <div aria-live="polite">
        {message && (
          <p className={message.erreur ? styles.erreur : styles.message} role={message.erreur ? "alert" : undefined}>
            {message.texte}
          </p>
        )}
      </div>

      <footer className={styles.pied}>
        <p>Accès réservé</p>
        <p>
          Besoin d’aide ? <a href="mailto:contact@atelier.local">Contactez l’équipe</a>
        </p>
      </footer>
    </main>
  );
}
