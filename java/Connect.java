import java.sql.*;
import java.util.logging.Level;
import java.util.logging.Logger;

public class Connect {
    public static void main( String args[] ) {
	Connection conn = null;
	Statement st    = null;
	ResultSet rs    = null;

	String url  = "jdbc:mysql://mysql.chrismerlo.net:3306/album_credits";
	String user = "cmerlo";
	String pw   = "sharkinfestedpiss";

	try {
	    Class.forName("com.mysql.jdbc.Driver").newInstance();

	    conn = DriverManager.getConnection( url, user, pw );
	    st = conn.createStatement();
	    rs = st.executeQuery( "show tables" );

	    if( rs.next() )
		System.out.println( rs.getString( 1 ) );

	} catch( SQLException ex ) {
	    Logger lgr = Logger.getLogger( Connect.class.getName() );
	    lgr.log( Level.SEVERE, ex.getMessage(), ex );
	} catch( ClassNotFoundException ex ) {
	    Logger lgr = Logger.getLogger( Connect.class.getName() );
	    lgr.log( Level.SEVERE, ex.getMessage(), ex );
	} catch(InstantiationException e) {
	    System.out.println(e.toString());
	} catch(IllegalAccessException e) {
	    System.out.println(e.toString());
	} finally {
	    try {
		if( rs != null )
		    rs.close();
		if( st != null )
		    st.close();
		if( conn != null )
		    conn.close();
	    } catch( SQLException ex ) {
		Logger lgr = Logger.getLogger( Connect.class.getName() );
		lgr.log( Level.WARNING, ex.getMessage(), ex );
	    }
	}
    }
}
