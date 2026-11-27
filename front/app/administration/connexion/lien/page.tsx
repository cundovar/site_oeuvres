"use client";

import { Suspense, useEffect, useState } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import styles from "./lien.module.scss";

function Confirmation() {
  const navigateur = useRouter();
  const parametres = useSearchParams();
  const [initial] = useState(() => {
    const complets = parametres.get("user") !== null
      && parametres.get("hash") !== null
      && parametres.get("expires") !== null;
    return complets ? new URLSearchParams(parametres.toString()) : null;
  });
  const [etat, setEtat] = useState<"pret" | "incomplet" | "enCours" | "echec">(
    initial !== null ? "pret" : "incomplet",
  );

  useEffect(() => {
    navigateur.replace("/administration/connexion/lien");
  }, [navigateur]);

  async function confirmer() {
    if (initial === null) {
      return;
    }
    setEtat("enCours");
    try {
      const reponse = await fetch("/api/admin/connexion/lien/valide", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: initial.toString(),
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

  if (etat === "incomplet") {
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
      <button className={styles.bouton} onClick={confirmer} disabled={etat !== "pret"}>
        {etat === "pret" ? "Confirmer la connexion" : "Confirmation…"}
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
