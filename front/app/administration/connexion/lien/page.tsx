"use client";

import { Suspense, useState } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import styles from "./lien.module.scss";

function Confirmation() {
  const navigateur = useRouter();
  const parametres = useSearchParams();
  const [etat, setEtat] = useState<"attente" | "enCours" | "echec">("attente");

  async function confirmer() {
    setEtat("enCours");
    const requete = new URLSearchParams(parametres.toString());
    try {
      const reponse = await fetch(`/api/admin/connexion/lien/valide?${requete.toString()}`, {
        method: "POST",
      });
      if (reponse.ok) {
        navigateur.replace("/administration");
        return;
      }
      setEtat("echec");
    } catch {
      setEtat("echec");
    }
  }

  if (!parametres.get("user") || !parametres.get("hash") || !parametres.get("expires")) {
    return (
      <p className={styles.erreur} role="alert">
        Lien incomplet. Redemandez un lien de connexion.
      </p>
    );
  }

  return (
    <>
      <p className={styles.texte}>
        Pour ouvrir votre session, confirmez la connexion. Ce lien est valable 15 minutes et à usage unique.
      </p>
      <button className={styles.bouton} onClick={confirmer} disabled={etat !== "attente"}>
        {etat === "attente" ? "Confirmer la connexion" : "Confirmation…"}
      </button>
      {etat === "echec" && (
        <p className={styles.erreur} role="alert">
          Lien expiré ou déjà utilisé.{" "}
          <a href="/administration/connexion">Redemander un lien</a>
        </p>
      )}
    </>
  );
}

export default function LienConnexion() {
  return (
    <main className={styles.main}>
      <p className={styles.atelier}>L’ATELIER</p>
      <h1>Confirmation de connexion</h1>
      <Suspense fallback={<p className={styles.texte}>Chargement…</p>}>
        <Confirmation />
      </Suspense>
    </main>
  );
}
