"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import styles from "./administration.module.scss";

export default function Administration() {
  const navigateur = useRouter();
  const [etat, setEtat] = useState<"verification" | "connecte" | "refus">("verification");
  const [email, setEmail] = useState("");
  const [enCours, setEnCours] = useState(false);
  const [message, setMessage] = useState<string | null>(null);

  useEffect(() => {
    let annule = false;
    fetch("/api/admin/session", { cache: "no-store" }).then(async (reponse) => {
      if (annule) return;
      if (reponse.status === 401 || reponse.status === 403) {
        navigateur.replace("/administration/connexion");
        return;
      }
      if (!reponse.ok) {
        setEtat("refus");
        return;
      }
      const donnees = (await reponse.json()) as { email: string | null };
      if (annule) return;
      if (!donnees.email) {
        navigateur.replace("/administration/connexion");
        return;
      }
      setEmail(donnees.email);
      setEtat("connecte");
    });
    return () => {
      annule = true;
    };
  }, [navigateur]);

  async function seDeconnecter() {
    setEnCours(true);
    setMessage(null);
    try {
      const csrf = await fetch("/api/admin/csrf", { cache: "no-store" });
      const { csrf: jeton } = (await csrf.json()) as { csrf: string };
      const reponse = await fetch("/api/admin/deconnexion", {
        method: "POST",
        headers: { "X-CSRF-Token": jeton },
      });
      if (reponse.ok) {
        navigateur.replace("/administration/connexion");
        return;
      }
      setMessage("La déconnexion n’a pas abouti. Réessayez.");
    } catch {
      setMessage("Service indisponible. Réessayez plus tard.");
    } finally {
      setEnCours(false);
    }
  }

  if (etat === "verification") {
    return (
      <main className={styles.main}>
        <p className={styles.atelier}>L’ATELIER</p>
        <p>Chargement…</p>
      </main>
    );
  }

  if (etat === "refus") {
    return (
      <main className={styles.main}>
        <p className={styles.atelier}>L’ATELIER</p>
        <p role="alert">Le service de gestion est momentanément indisponible. Réessayez plus tard.</p>
      </main>
    );
  }

  return (
    <main className={styles.main}>
      <p className={styles.atelier}>L’ATELIER</p>
      <h1>Espace de gestion du site</h1>
      <p className={styles.email}>Connecté avec {email}</p>
      <p className={styles.avertissement} role="note">
        La gestion des œuvres n’est pas encore disponible : ce premier espace confirme la session sécurisée.
      </p>
      <button className={styles.bouton} onClick={seDeconnecter} disabled={enCours}>
        {enCours ? "Déconnexion…" : "Se déconnecter"}
      </button>
      {message && (
        <div aria-live="polite">
          <p className={styles.erreur} role="alert">
            {message}
          </p>
        </div>
      )}
    </main>
  );
}
