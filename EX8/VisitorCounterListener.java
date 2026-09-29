import javax.servlet.ServletContext;
import javax.servlet.annotation.WebListener;
import javax.servlet.http.HttpSessionEvent;
import javax.servlet.http.HttpSessionListener;

@WebListener
public class VisitorCounterListener implements HttpSessionListener {

    // Called automatically by the server every time a NEW session is created
    // (i.e., a new/unique visitor, since each browser gets one session)
    @Override
    public void sessionCreated(HttpSessionEvent se) {
        ServletContext context = se.getSession().getServletContext();

        Integer count = (Integer) context.getAttribute("visitorCount");
        if (count == null) {
            count = 0;
        }
        count = count + 1;

        // Store the updated count in ServletContext so ALL servlets can read it
        context.setAttribute("visitorCount", count);
    }

    // Called automatically when a session expires or is invalidated
    @Override
    public void sessionDestroyed(HttpSessionEvent se) {
        // Optional: no action needed, count of visitors so far should remain
    }
}
