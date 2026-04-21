import { useCallback, useState } from "react";
import {
  IonButton,
  IonContent,
  IonHeader,
  IonPage,
  IonTitle,
  IonToolbar,
  IonText,
  IonSpinner
} from "@ionic/react";
import { checkForUpdates, initUpdater } from "../updateClient";
import "./Home.css";

const Home: React.FC = () => {
  const [loading, setLoading] = useState(false);
  const [message, setMessage] = useState<string>("");
  const [detail, setDetail] = useState<string>("");

  const onCheck = useCallback(async () => {
    setLoading(true);
    setMessage("");
    setDetail("");
    try {
      await initUpdater();
      const result = await checkForUpdates();
      setMessage(
        result.hasUpdate ? "Update available" : "Up to date"
      );
      setDetail(JSON.stringify(result, null, 2));
    } catch (e) {
      setMessage("Error");
      setDetail(e instanceof Error ? e.message : String(e));
    } finally {
      setLoading(false);
    }
  }, []);

  return (
    <IonPage>
      <IonHeader>
        <IonToolbar>
          <IonTitle>Hybrid Updater</IonTitle>
        </IonToolbar>
      </IonHeader>
      <IonContent className="ion-padding">
        <IonText>
          <p>
            Point <code>VITE_LARAVEL_BASE_URL</code> at the fixture (e.g. emulator:{" "}
            <code>http://10.0.2.2:8080</code>).
          </p>
        </IonText>
        <IonButton expand="block" onClick={onCheck} disabled={loading}>
          {loading ? <IonSpinner name="crescent" /> : "Check for updates"}
        </IonButton>
        {message ? <h2>{message}</h2> : null}
        {detail ? (
          <pre style={{ fontSize: "12px", overflow: "auto" }}>{detail}</pre>
        ) : null}
      </IonContent>
    </IonPage>
  );
};

export default Home;
